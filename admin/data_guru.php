<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $nip      = sanitize($_POST['nip']);
        $nama     = sanitize($_POST['nama']);
        $jk       = sanitize($_POST['jenis_kelamin']);
        $username = sanitize($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role     = 'guru';

        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' UNION SELECT id FROM guru WHERE nip='$nip'");
        if (mysqli_num_rows($cek) > 0) {
            set_flash_message('danger', 'Gagal! Username atau NIP sudah terdaftar.');
        } else {
            mysqli_begin_transaction($conn);
            try {
                $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt1, "sss", $username, $password, $role);
                mysqli_stmt_execute($stmt1);
                $user_id = mysqli_insert_id($conn);

                $stmt2 = mysqli_prepare($conn, "INSERT INTO guru (nip, nama, jenis_kelamin, user_id) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, "sssi", $nip, $nama, $jk, $user_id);
                mysqli_stmt_execute($stmt2);

                mysqli_commit($conn);
                set_flash_message('success', 'Data Guru dan Akun berhasil ditambahkan!');
            } catch (Exception $e) {
                mysqli_rollback($conn);
                set_flash_message('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            }
        }
    } 
    elseif ($aksi === 'edit') {
        $id_guru = (int)$_POST['id_guru'];
        $nip     = sanitize($_POST['nip']);
        $nama    = sanitize($_POST['nama']);
        $jk      = sanitize($_POST['jenis_kelamin']);

        $stmt = mysqli_prepare($conn, "UPDATE guru SET nip=?, nama=?, jenis_kelamin=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $nip, $nama, $jk, $id_guru);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Guru berhasil diperbarui!');
        } else {
            set_flash_message('danger', 'Gagal memperbarui data Guru.');
        }
    }
    elseif ($aksi === 'hapus') {
        $user_id = (int)$_POST['user_id'];
        
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Guru beserta akun berhasil dihapus!');
        } else {
            set_flash_message('danger', 'Gagal menghapus data.');
        }
    }

    header("Location: data_guru.php");
    exit();
}

$query = "SELECT g.*, u.username FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC";
$result = mysqli_query($conn, $query);
$teachers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $teachers[] = $row;
    }
}

$total_guru = count($teachers);
$total_guru_laki = count(array_filter($teachers, static fn($row) => ($row['jenis_kelamin'] ?? '') === 'L'));
$total_guru_perempuan = count(array_filter($teachers, static fn($row) => ($row['jenis_kelamin'] ?? '') === 'P'));
$data_guru_error = !$result;

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/data_guru_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
