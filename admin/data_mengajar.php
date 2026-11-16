<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

$e = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

// Proses penambahan dan penghapusan penugasan tetap menggunakan tabel mengajar.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = (string) $_POST['aksi'];

    if ($aksi === 'tambah') {
        $mapel_id = (int) ($_POST['mapel_id'] ?? 0);
        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);

        if ($mapel_id <= 0 || $siswa_id <= 0) {
            set_flash_message('danger', 'Pilih mata pelajaran dan siswa yang valid.');
        } else {
            // Pastikan kedua data induk tersedia sebelum membuat relasi.
            $stmt_validasi = mysqli_prepare(
                $conn,
                'SELECT m.id FROM mapel m CROSS JOIN siswa s WHERE m.id = ? AND s.id = ? LIMIT 1'
            );

            if (!$stmt_validasi) {
                set_flash_message('danger', 'Validasi penugasan tidak dapat dijalankan.');
            } else {
                mysqli_stmt_bind_param($stmt_validasi, 'ii', $mapel_id, $siswa_id);
                mysqli_stmt_execute($stmt_validasi);
                $validasi_result = mysqli_stmt_get_result($stmt_validasi);
                $data_valid = $validasi_result && mysqli_num_rows($validasi_result) === 1;
                mysqli_stmt_close($stmt_validasi);

                if (!$data_valid) {
                    set_flash_message('danger', 'Mata pelajaran atau siswa yang dipilih tidak ditemukan.');
                } else {
                    // Cegah relasi siswa-mapel ganda.
                    $stmt_cek = mysqli_prepare(
                        $conn,
                        'SELECT id FROM mengajar WHERE mapel_id = ? AND siswa_id = ? LIMIT 1'
                    );

                    if (!$stmt_cek) {
                        set_flash_message('danger', 'Pemeriksaan penugasan gagal dijalankan.');
                    } else {
                        mysqli_stmt_bind_param($stmt_cek, 'ii', $mapel_id, $siswa_id);
                        mysqli_stmt_execute($stmt_cek);
                        $cek_result = mysqli_stmt_get_result($stmt_cek);
                        $sudah_ada = $cek_result && mysqli_num_rows($cek_result) > 0;
                        mysqli_stmt_close($stmt_cek);

                        if ($sudah_ada) {
                            set_flash_message('warning', 'Siswa tersebut sudah terdaftar pada mata pelajaran ini.');
                        } else {
                            $stmt = mysqli_prepare($conn, 'INSERT INTO mengajar (mapel_id, siswa_id) VALUES (?, ?)');
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, 'ii', $mapel_id, $siswa_id);
                                if (mysqli_stmt_execute($stmt)) {
                                    set_flash_message('success', 'Penugasan siswa berhasil ditambahkan!');
                                } else {
                                    set_flash_message('danger', 'Gagal menyimpan penugasan siswa.');
                                }
                                mysqli_stmt_close($stmt);
                            } else {
                                set_flash_message('danger', 'Penugasan tidak dapat disimpan saat ini.');
                            }
                        }
                    }
                }
            }
        }
    } elseif ($aksi === 'hapus') {
        $id_mengajar = (int) ($_POST['id_mengajar'] ?? 0);

        if ($id_mengajar <= 0) {
            set_flash_message('danger', 'Penugasan yang dipilih tidak valid.');
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM mengajar WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id_mengajar);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    set_flash_message('success', 'Penugasan berhasil dihapus!');
                } else {
                    set_flash_message('danger', 'Gagal menghapus penugasan. Data mungkin sudah tidak tersedia.');
                }
                mysqli_stmt_close($stmt);
            } else {
                set_flash_message('danger', 'Permintaan penghapusan tidak dapat diproses.');
            }
        }
    } else {
        set_flash_message('danger', 'Aksi penugasan tidak dikenali.');
    }

    header('Location: data_mengajar.php');
    exit();
}

// Daftar penugasan beserta identitas mapel, guru, dan siswa.
$query = "
    SELECT
        mg.id,
        mg.mapel_id,
        mg.siswa_id,
        m.nama_mapel,
        m.kode_mapel,
        g.nama AS nama_guru,
        s.nama AS nama_siswa,
        s.nis,
        s.kelas
    FROM mengajar mg
    JOIN mapel m ON mg.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    JOIN siswa s ON mg.siswa_id = s.id
    ORDER BY m.nama_mapel ASC, s.kelas ASC, s.nama ASC
";
$result_penugasan = mysqli_query($conn, $query);
$penugasan = [];
if ($result_penugasan) {
    while ($row = mysqli_fetch_assoc($result_penugasan)) {
        $penugasan[] = $row;
    }
}

// Opsi modal disusun sebagai array agar nilai dari database selalu di-escape saat dirender.
$mapel_list = [];
$q_mapel = mysqli_query(
    $conn,
    'SELECT m.id, m.kode_mapel, m.nama_mapel, g.nama AS nama_guru FROM mapel m LEFT JOIN guru g ON m.guru_id = g.id ORDER BY m.nama_mapel ASC'
);
if ($q_mapel) {
    while ($row = mysqli_fetch_assoc($q_mapel)) {
        $mapel_list[] = $row;
    }
}

$siswa_list = [];
$q_siswa = mysqli_query($conn, 'SELECT id, nis, nama, kelas FROM siswa ORDER BY kelas ASC, nama ASC');
if ($q_siswa) {
    while ($row = mysqli_fetch_assoc($q_siswa)) {
        $siswa_list[] = $row;
    }
}

$total_penugasan = count($penugasan);
$unique_mapel = [];
$unique_siswa = [];
foreach ($penugasan as $row) {
    $unique_mapel[(int) $row['mapel_id']] = true;
    $unique_siswa[(int) $row['siswa_id']] = true;
}
$jumlah_mapel_terpakai = count($unique_mapel);
$jumlah_siswa_terdaftar = count($unique_siswa);
$query_error = !$result_penugasan || !$q_mapel || !$q_siswa;

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/data_mengajar_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
