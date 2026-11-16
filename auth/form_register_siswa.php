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
        $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'siswa')");
        mysqli_stmt_bind_param($stmt1, "ss", $username, $password);
        mysqli_stmt_execute($stmt1);
        $user_id = mysqli_insert_id($conn);

        $stmt2 = mysqli_prepare($conn, "INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, "ssssi", $nis, $nama, $kelas, $jk, $user_id);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($conn);
        set_flash_message('success', 'Registrasi berhasil! Silakan login.');
        header('Location: ' . BASE_URL . 'auth/form_login.php');
        exit();
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        set_flash_message('danger', 'Gagal registrasi! NIS atau Username mungkin sudah terdaftar.');
    }
}

require __DIR__ . '/views/form_register_siswa_view.php';
