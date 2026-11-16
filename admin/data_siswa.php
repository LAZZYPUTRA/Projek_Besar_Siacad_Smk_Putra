<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $nis      = sanitize($_POST['nis']);
        $nama     = sanitize($_POST['nama']);
        $kelas    = sanitize($_POST['kelas']);
        $jk       = sanitize($_POST['jenis_kelamin']);
        $username = sanitize($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
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
    elseif ($aksi === 'edit') {
        $id_siswa = (int)$_POST['id_siswa'];
        $nis      = sanitize($_POST['nis']);
        $nama     = sanitize($_POST['nama']);
        $kelas    = sanitize($_POST['kelas']);
        $jk       = sanitize($_POST['jenis_kelamin']);

        $stmt = mysqli_prepare($conn, "UPDATE siswa SET nis=?, nama=?, kelas=?, jenis_kelamin=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nis, $nama, $kelas, $jk, $id_siswa);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Siswa berhasil diperbarui!');
        } else {
            set_flash_message('danger', 'Gagal memperbarui data Siswa.');
        }
    }
    elseif ($aksi === 'hapus') {
        $user_id = (int)$_POST['user_id'];
        
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
        $students[] = $row;
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
