<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['guru']);

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$id_guru = 0;
$nama_guru = 'Guru';
$guru_error = false;

$escape = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

// CSRF token khusus formulir pengelolaan nilai pada halaman ini.
if (empty($_SESSION['sipn_grade_csrf'])) {
    $_SESSION['sipn_grade_csrf'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['sipn_grade_csrf'];

// Ambil identitas guru berdasarkan akun yang sedang aktif.
$stmt_guru = $conn->prepare('SELECT id, nama FROM guru WHERE user_id = ? LIMIT 1');
if ($stmt_guru) {
    $stmt_guru->bind_param('i', $user_id);
    $stmt_guru->execute();
    $result_guru = $stmt_guru->get_result();
    $data_guru = $result_guru ? $result_guru->fetch_assoc() : null;
    $stmt_guru->close();

    if ($data_guru) {
        $id_guru = (int) $data_guru['id'];
        $nama_guru = (string) $data_guru['nama'];
    } else {
        $guru_error = true;
    }
} else {
    $guru_error = true;
    error_log('SIPN: Gagal menyiapkan query identitas guru: ' . $conn->error);
}

// Semua aksi POST diarahkan kembali ke halaman ini agar flash message tampil satu kali.
$redirect_to_page = static function () {
    header('Location: ' . BASE_URL . 'guru/data_nilai_siswa.php');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($csrf_token, $posted_token)) {
        set_flash_message('danger', 'Sesi formulir tidak valid atau telah kedaluwarsa. Silakan coba kembali.');
        $redirect_to_page();
    }

    if ($id_guru <= 0) {
        set_flash_message('danger', 'Profil guru tidak ditemukan. Hubungi administrator sekolah.');
        $redirect_to_page();
    }

    // Simpan atau perbarui nilai rapor.
    if (isset($_POST['simpan_nilai'])) {
        $siswa_id = filter_var($_POST['siswa_id'] ?? null, FILTER_VALIDATE_INT);
        $mapel_id = filter_var($_POST['mapel_id'] ?? null, FILTER_VALIDATE_INT);
        $nilai_kkm = filter_var($_POST['nilai_kkm'] ?? null, FILTER_VALIDATE_INT);
        $nilai_pengetahuan = filter_var($_POST['nilai_pengetahuan'] ?? null, FILTER_VALIDATE_INT);
        $nilai_keterampilan = filter_var($_POST['nilai_keterampilan'] ?? null, FILTER_VALIDATE_INT);

        $nilai_valid = (
            $siswa_id !== false && $siswa_id > 0 &&
            $mapel_id !== false && $mapel_id > 0 &&
            $nilai_kkm !== false && $nilai_kkm >= 0 && $nilai_kkm <= 100 &&
            $nilai_pengetahuan !== false && $nilai_pengetahuan >= 0 && $nilai_pengetahuan <= 100 &&
            $nilai_keterampilan !== false && $nilai_keterampilan >= 0 && $nilai_keterampilan <= 100
        );

        if (!$nilai_valid) {
            set_flash_message('warning', 'Periksa kembali pilihan siswa, mata pelajaran, KKM, dan nilai. Semua nilai harus berada pada rentang 0–100.');
            $redirect_to_page();
        }

        // Validasi kepemilikan mapel dilakukan di server, bukan hanya mengandalkan isi dropdown.
        $stmt_mapel = $conn->prepare('SELECT id FROM mapel WHERE id = ? AND guru_id = ? LIMIT 1');
        $mapel_milik_guru = false;
        if ($stmt_mapel) {
            $stmt_mapel->bind_param('ii', $mapel_id, $id_guru);
            $stmt_mapel->execute();
            $result_mapel = $stmt_mapel->get_result();
            $mapel_milik_guru = $result_mapel && $result_mapel->num_rows > 0;
            $stmt_mapel->close();
        }

        // Pastikan siswa yang dipilih benar-benar terdaftar.
        $stmt_siswa = $conn->prepare('SELECT id FROM siswa WHERE id = ? LIMIT 1');
        $siswa_terdaftar = false;
        if ($stmt_siswa) {
            $stmt_siswa->bind_param('i', $siswa_id);
            $stmt_siswa->execute();
            $result_siswa = $stmt_siswa->get_result();
            $siswa_terdaftar = $result_siswa && $result_siswa->num_rows > 0;
            $stmt_siswa->close();
        }

        if (!$mapel_milik_guru || !$siswa_terdaftar) {
            set_flash_message('danger', 'Mata pelajaran atau siswa tidak valid untuk akun guru ini.');
            $redirect_to_page();
        }

        $rata_rata = ($nilai_pengetahuan + $nilai_keterampilan) / 2;
        $keterangan = ($rata_rata >= $nilai_kkm) ? 'Tuntas' : 'Belum Tuntas';

        // Pertahankan mekanisme upsert yang digunakan sistem sebelumnya.
        $stmt_cek = $conn->prepare('SELECT id FROM nilai_rapor WHERE siswa_id = ? AND mapel_id = ? LIMIT 1');
        if (!$stmt_cek) {
            error_log('SIPN: Gagal memeriksa entri nilai sebelumnya: ' . $conn->error);
            set_flash_message('danger', 'Data nilai belum dapat diperiksa. Silakan coba kembali.');
            $redirect_to_page();
        }

        $stmt_cek->bind_param('ii', $siswa_id, $mapel_id);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $nilai_sudah_ada = $result_cek && $result_cek->num_rows > 0;
        $stmt_cek->close();

        if ($nilai_sudah_ada) {
            $stmt_simpan = $conn->prepare(
                'UPDATE nilai_rapor
                 SET nilai_kkm = ?, nilai_pengetahuan = ?, nilai_keterampilan = ?, Keterangan = ?
                 WHERE siswa_id = ? AND mapel_id = ?'
            );
            if ($stmt_simpan) {
                $stmt_simpan->bind_param(
                    'iiisii',
                    $nilai_kkm,
                    $nilai_pengetahuan,
                    $nilai_keterampilan,
                    $keterangan,
                    $siswa_id,
                    $mapel_id
                );
            }
        } else {
            $stmt_simpan = $conn->prepare(
                'INSERT INTO nilai_rapor
                 (siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, Keterangan)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            if ($stmt_simpan) {
                $stmt_simpan->bind_param(
                    'iiiiis',
                    $siswa_id,
                    $mapel_id,
                    $nilai_kkm,
                    $nilai_pengetahuan,
                    $nilai_keterampilan,
                    $keterangan
                );
            }
        }

        if (isset($stmt_simpan) && $stmt_simpan && $stmt_simpan->execute()) {
            set_flash_message('success', 'Data nilai rapor berhasil disimpan.');
        } else {
            set_flash_message('danger', 'Gagal menyimpan data nilai. Silakan periksa koneksi database.');
            error_log('SIPN: Gagal menyimpan nilai rapor. ' . $conn->error);
        }

        if (isset($stmt_simpan) && $stmt_simpan) {
            $stmt_simpan->close();
        }

        $redirect_to_page();
    }

    // Hapus nilai hanya jika data tersebut berada pada mata pelajaran milik guru aktif.
    if (isset($_POST['aksi']) && $_POST['aksi'] === 'hapus_nilai') {
        $id_nilai = filter_var($_POST['id_nilai'] ?? null, FILTER_VALIDATE_INT);

        if ($id_nilai === false || $id_nilai <= 0) {
            set_flash_message('warning', 'Data nilai yang dipilih tidak valid.');
            $redirect_to_page();
        }

        $stmt_hapus = $conn->prepare(
            'DELETE nr
             FROM nilai_rapor nr
             INNER JOIN mapel m ON nr.mapel_id = m.id
             WHERE nr.id = ? AND m.guru_id = ?'
        );

        if ($stmt_hapus) {
            $stmt_hapus->bind_param('ii', $id_nilai, $id_guru);
            $hapus_berhasil = $stmt_hapus->execute() && $stmt_hapus->affected_rows > 0;
            $stmt_hapus->close();

            if ($hapus_berhasil) {
                set_flash_message('success', 'Data nilai rapor berhasil dihapus.');
            } else {
                set_flash_message('warning', 'Data tidak ditemukan atau bukan bagian dari mata pelajaran Anda.');
            }
        } else {
            set_flash_message('danger', 'Gagal menyiapkan proses penghapusan data.');
            error_log('SIPN: Gagal menyiapkan hapus nilai: ' . $conn->error);
        }

        $redirect_to_page();
    }
}

// Ambil mata pelajaran yang dimiliki guru aktif.
$mapel_list = [];
if ($id_guru > 0) {
    $stmt_mapel_list = $conn->prepare(
        'SELECT id, kode_mapel, nama_mapel
         FROM mapel
         WHERE guru_id = ?
         ORDER BY nama_mapel ASC'
    );

    if ($stmt_mapel_list) {
        $stmt_mapel_list->bind_param('i', $id_guru);
        $stmt_mapel_list->execute();
        $result_mapel_list = $stmt_mapel_list->get_result();
        while ($result_mapel_list && ($mapel = $result_mapel_list->fetch_assoc())) {
            $mapel_list[] = $mapel;
        }
        $stmt_mapel_list->close();
    }
}

// Daftar siswa dipertahankan sesuai alur halaman sebelumnya.
$siswa_list = [];
$stmt_siswa_list = $conn->prepare('SELECT id, nis, nama, kelas FROM siswa ORDER BY nama ASC');
if ($stmt_siswa_list) {
    $stmt_siswa_list->execute();
    $result_siswa_list = $stmt_siswa_list->get_result();
    while ($result_siswa_list && ($siswa = $result_siswa_list->fetch_assoc())) {
        $siswa_list[] = $siswa;
    }
    $stmt_siswa_list->close();
}

// Tampilkan hanya nilai yang berada pada mata pelajaran milik guru yang sedang login.
$nilai_list = [];
$stmt_nilai = $conn->prepare(
    'SELECT
        nr.id,
        nr.siswa_id,
        nr.mapel_id,
        nr.nilai_kkm,
        nr.nilai_pengetahuan,
        nr.nilai_keterampilan,
        nr.Keterangan,
        s.nama AS nama_siswa,
        s.nis,
        s.kelas,
        m.kode_mapel,
        m.nama_mapel
     FROM nilai_rapor nr
     INNER JOIN siswa s ON nr.siswa_id = s.id
     INNER JOIN mapel m ON nr.mapel_id = m.id
     WHERE m.guru_id = ?
     ORDER BY s.kelas ASC, s.nama ASC, m.nama_mapel ASC'
);

if ($stmt_nilai) {
    $stmt_nilai->bind_param('i', $id_guru);
    $stmt_nilai->execute();
    $result_nilai = $stmt_nilai->get_result();
    while ($result_nilai && ($nilai = $result_nilai->fetch_assoc())) {
        $nilai_list[] = $nilai;
    }
    $stmt_nilai->close();
} else {
    error_log('SIPN: Gagal menyiapkan daftar nilai guru: ' . $conn->error);
}

$total_nilai = count($nilai_list);
$total_tuntas = 0;
$total_belum_tuntas = 0;

foreach ($nilai_list as $nilai) {
    if (strtolower(trim((string) $nilai['Keterangan'])) === 'tuntas') {
        $total_tuntas++;
    } else {
        $total_belum_tuntas++;
    }
}

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/data_nilai_siswa_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
