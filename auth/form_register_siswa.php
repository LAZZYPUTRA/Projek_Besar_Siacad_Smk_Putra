<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nis      = sanitize($_POST['nis']);
    $nama     = sanitize($_POST['nama']);
    $kelas    = sanitize($_POST['kelas']);
    $jk       = sanitize($_POST['jenis_kelamin']);

    mysqli_begin_transaction($conn);
    try {
        // 1. Insert ke tabel users
        $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'siswa')");
        mysqli_stmt_bind_param($stmt1, "ss", $username, $password);
        mysqli_stmt_execute($stmt1);
        $user_id = mysqli_insert_id($conn);

        // 2. Insert ke tabel siswa
        $stmt2 = mysqli_prepare($conn, "INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, "ssssi", $nis, $nama, $kelas, $jk, $user_id);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($conn);
        set_flash_message('success', 'Registrasi berhasil! Silakan login.');
        header('Location: form_login.php');
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        set_flash_message('danger', 'Gagal registrasi NIS/Username mungkin sudah ada.');
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Siswa - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-5">
<div class="container" style="max-width: 500px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="card-title text-center mb-4">Registrasi Siswa</h3>
            <?php display_flash_message(); ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">NIS</label>
                    <input type="text" name="nis" class="form-control" maxlength="10" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" class="form-control" placeholder="Contoh: XII RPL 1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Daftar Sekarang</button>
            </form>
            <div class="text-center mt-3">
                <a href="form_login.php" class="text-decoration-none">Sudah punya akun? Login di sini</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>