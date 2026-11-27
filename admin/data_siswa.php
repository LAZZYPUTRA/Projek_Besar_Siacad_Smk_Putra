<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

/*
 * Ambil batas panjang kolom NIS dari definisi kolom MySQL.
 * SHOW COLUMNS memberikan tipe seperti varchar(12), lalu panjangnya
 * dipakai bersama untuk validasi server dan atribut maxlength di form.
 * Fallback 20 hanya digunakan jika metadata kolom tidak dapat dibaca.
 */
$nis_max_length = 20;
$nisColumnResult = mysqli_query($conn, "SHOW COLUMNS FROM `siswa` LIKE 'nis'");

if ($nisColumnResult instanceof mysqli_result) {
    $nisColumn = mysqli_fetch_assoc($nisColumnResult);
    $columnType = strtolower((string) ($nisColumn['Type'] ?? ''));

    if (preg_match('/^(?:var)?char\((\d+)\)/', $columnType, $matches)) {
        $detectedLength = (int) $matches[1];
        if ($detectedLength > 0) {
            $nis_max_length = $detectedLength;
        }
    }

    mysqli_free_result($nisColumnResult);
}

$validasi_nis = static function (string $nis, int $batas): ?string {
    if ($nis === '') {
        return 'NIS wajib diisi.';
    }

    if (!preg_match('/^[0-9]+$/', $nis)) {
        return 'NIS hanya boleh berisi angka.';
    }

    if (strlen($nis) > $batas) {
        return "NIS terlalu panjang. Maksimal {$batas} digit.";
    }

    return null;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $nis_input = trim((string) ($_POST['nis'] ?? ''));
        $pesan_validasi_nis = $validasi_nis($nis_input, $nis_max_length);

        if ($pesan_validasi_nis !== null) {
            set_flash_message('danger', $pesan_validasi_nis);
        } else {
            $nis      = sanitize($nis_input);
            $nama     = sanitize($_POST['nama'] ?? '');
            $kelas    = sanitize($_POST['kelas'] ?? '');
            $jk       = sanitize($_POST['jenis_kelamin'] ?? '');
            $username = sanitize($_POST['username'] ?? '');
            $password = password_hash((string) ($_POST['password'] ?? ''), PASSWORD_DEFAULT);
            $role     = 'siswa';

            $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' UNION SELECT id FROM siswa WHERE nis='$nis'");
            if (mysqli_num_rows($cek) > 0) {
                set_flash_message('danger', 'Gagal! Username atau NIS sudah terdaftar.');
            } else {
                mysqli_begin_transaction($conn);
                try {
                    $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt1, "sss", $username, $password, $role);
                    mysqli_stmt_execute($stmt1);
                    $user_id = mysqli_insert_id($conn);

                    $stmt2 = mysqli_prepare($conn, "INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt2, "ssssi", $nis, $nama, $kelas, $jk, $user_id);
                    mysqli_stmt_execute($stmt2);

                    mysqli_commit($conn);
                    set_flash_message('success', 'Data Siswa dan Akun berhasil ditambahkan!');
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    set_flash_message('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
                }
            }
        }
    }
    elseif ($aksi === 'edit') {
        $nis_input = trim((string) ($_POST['nis'] ?? ''));
        $pesan_validasi_nis = $validasi_nis($nis_input, $nis_max_length);

        if ($pesan_validasi_nis !== null) {
            set_flash_message('danger', $pesan_validasi_nis);
        } else {
            $id_siswa = (int) ($_POST['id_siswa'] ?? 0);
            $nis      = sanitize($nis_input);
            $nama     = sanitize($_POST['nama'] ?? '');
            $kelas    = sanitize($_POST['kelas'] ?? '');
            $jk       = sanitize($_POST['jenis_kelamin'] ?? '');

            $stmt = mysqli_prepare($conn, "UPDATE siswa SET nis=?, nama=?, kelas=?, jenis_kelamin=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $nis, $nama, $kelas, $jk, $id_siswa);

            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Data Siswa berhasil diperbarui!');
            } else {
                set_flash_message('danger', 'Gagal memperbarui data Siswa.');
            }
        }
    }
    elseif ($aksi === 'hapus') {
        $user_id = (int) ($_POST['user_id'] ?? 0);

        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);

        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Siswa beserta akun berhasil dihapus!');
        } else {
            set_flash_message('danger', 'Gagal menghapus data.');
        }
    }

    header("Location: data_siswa.php");
    exit();
}

$query = "SELECT s.*, u.username FROM siswa s JOIN users u ON s.user_id = u.id ORDER BY s.kelas ASC, s.nama ASC";
$result = mysqli_query($conn, $query);
$students = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $rows;
    }
}

$total_siswa = count($students);
$total_kelas = count(array_unique(array_map(static fn($row) => (string) ($row['kelas'] ?? ''), $students)));
$total_siswa_laki = count(array_filter($students, static fn($row) => ($row['jenis_kelamin'] ?? '') === 'L'));
$total_siswa_perempuan = count(array_filter($students, static fn($row) => ($row['jenis_kelamin'] ?? '') === 'P'));
$data_siswa_error = !$result;

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/data_siswa_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
