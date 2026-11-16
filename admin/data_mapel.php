<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

$e = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = (string) $_POST['aksi'];

    if ($aksi === 'tambah' || $aksi === 'edit') {
        $id_mapel = (int) ($_POST['id_mapel'] ?? 0);
        $kode = trim((string) ($_POST['kode_mapel'] ?? ''));
        $nama = trim((string) ($_POST['nama_mapel'] ?? ''));
        $guru_id = (int) ($_POST['guru_id'] ?? 0);

        if ($kode === '' || $nama === '' || $guru_id <= 0 || ($aksi === 'edit' && $id_mapel <= 0)) {
            set_flash_message('danger', 'Lengkapi kode, nama mata pelajaran, dan guru pengampu dengan benar.');
        } else {
            $stmt_guru = mysqli_prepare($conn, 'SELECT id FROM guru WHERE id = ? LIMIT 1');
            $guru_valid = false;
            if ($stmt_guru) {
                mysqli_stmt_bind_param($stmt_guru, 'i', $guru_id);
                mysqli_stmt_execute($stmt_guru);
                $guru_result = mysqli_stmt_get_result($stmt_guru);
                $guru_valid = $guru_result && mysqli_num_rows($guru_result) === 1;
                mysqli_stmt_close($stmt_guru);
            }

            if (!$guru_valid) {
                set_flash_message('danger', 'Guru pengampu yang dipilih tidak ditemukan.');
            } else {
                $stmt_cek = mysqli_prepare(
                    $conn,
                    $aksi === 'edit'
                        ? 'SELECT id FROM mapel WHERE kode_mapel = ? AND id <> ? LIMIT 1'
                        : 'SELECT id FROM mapel WHERE kode_mapel = ? LIMIT 1'
                );
                $kode_dipakai = false;
                $cek_kode_gagal = !$stmt_cek;

                if ($stmt_cek) {
                    if ($aksi === 'edit') {
                        mysqli_stmt_bind_param($stmt_cek, 'si', $kode, $id_mapel);
                    } else {
                        mysqli_stmt_bind_param($stmt_cek, 's', $kode);
                    }
                    if (mysqli_stmt_execute($stmt_cek)) {
                        $kode_result = mysqli_stmt_get_result($stmt_cek);
                        $kode_dipakai = $kode_result && mysqli_num_rows($kode_result) > 0;
                    } else {
                        $cek_kode_gagal = true;
                    }
                    mysqli_stmt_close($stmt_cek);
                }

                if ($cek_kode_gagal) {
                    set_flash_message('danger', 'Pemeriksaan kode mata pelajaran gagal. Silakan coba kembali.');
                } elseif ($kode_dipakai) {
                    set_flash_message('danger', 'Kode mata pelajaran sudah digunakan. Gunakan kode yang berbeda.');
                } elseif ($aksi === 'tambah') {
                    $stmt = mysqli_prepare($conn, 'INSERT INTO mapel (kode_mapel, nama_mapel, guru_id) VALUES (?, ?, ?)');
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'ssi', $kode, $nama, $guru_id);
                        if (mysqli_stmt_execute($stmt)) {
                            set_flash_message('success', 'Mata pelajaran berhasil ditambahkan!');
                        } else {
                            set_flash_message('danger', 'Gagal menambahkan mata pelajaran. Periksa kembali data yang dimasukkan.');
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        set_flash_message('danger', 'Data mata pelajaran tidak dapat disimpan saat ini.');
                    }
                } else {
                    $stmt = mysqli_prepare($conn, 'UPDATE mapel SET kode_mapel = ?, nama_mapel = ?, guru_id = ? WHERE id = ?');
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'ssii', $kode, $nama, $guru_id, $id_mapel);
                        if (mysqli_stmt_execute($stmt)) {
                            set_flash_message('success', 'Mata pelajaran berhasil diperbarui!');
                        } else {
                            set_flash_message('danger', 'Gagal memperbarui mata pelajaran.');
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        set_flash_message('danger', 'Perubahan mata pelajaran tidak dapat diproses saat ini.');
                    }
                }
            }
        }
    } elseif ($aksi === 'hapus') {
        $id_mapel = (int) ($_POST['id_mapel'] ?? 0);
        if ($id_mapel <= 0) {
            set_flash_message('danger', 'Mata pelajaran yang dipilih tidak valid.');
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM mapel WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id_mapel);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    set_flash_message('success', 'Mata pelajaran berhasil dihapus!');
                } else {
                    set_flash_message('danger', 'Gagal menghapus mata pelajaran. Pastikan mata pelajaran tidak sedang digunakan pada penugasan atau nilai rapor.');
                }
                mysqli_stmt_close($stmt);
            } else {
                set_flash_message('danger', 'Permintaan penghapusan mata pelajaran tidak dapat diproses.');
            }
        }
    } else {
        set_flash_message('danger', 'Aksi mata pelajaran tidak dikenali.');
    }

    header('Location: data_mapel.php');
    exit();
}

// Ambil guru untuk pilihan formulir.
$guru_list = [];
$guru_result = mysqli_query($conn, 'SELECT id, nama FROM guru ORDER BY nama ASC');
if ($guru_result) {
    while ($row = mysqli_fetch_assoc($guru_result)) {
        $guru_list[] = $row;
    }
}

// Jumlah peserta didik dihitung dari relasi mengajar pada setiap mata pelajaran.
$query = "
    SELECT
        m.id,
        m.kode_mapel,
        m.nama_mapel,
        m.guru_id,
        g.nama AS nama_guru,
        COUNT(DISTINCT mg.siswa_id) AS total_siswa
    FROM mapel m
    LEFT JOIN guru g ON m.guru_id = g.id
    LEFT JOIN mengajar mg ON mg.mapel_id = m.id
    GROUP BY m.id, m.kode_mapel, m.nama_mapel, m.guru_id, g.nama
    ORDER BY m.kode_mapel ASC, m.nama_mapel ASC
";
$result_mapel = mysqli_query($conn, $query);
$mapel_list = [];
if ($result_mapel) {
    while ($row = mysqli_fetch_assoc($result_mapel)) {
        $mapel_list[] = $row;
    }
}

$total_mapel = count($mapel_list);
$guru_terlibat = [];
$total_penugasan = 0;
foreach ($mapel_list as $row) {
    if (!empty($row['guru_id'])) {
        $guru_terlibat[(int) $row['guru_id']] = true;
    }
    $total_penugasan += (int) $row['total_siswa'];
}
$jumlah_guru_pengampu = count($guru_terlibat);
$query_error = !$guru_result || !$result_mapel;

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/data_mapel_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
