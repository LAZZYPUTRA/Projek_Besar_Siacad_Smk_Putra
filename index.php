<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/koneksi_database.php';

// Jika pengguna SUDAH login, langsung lempar ke dashboard masing-masing
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: " . BASE_URL . "admin/dashboard_admin.php");
            exit();
        case 'guru':
            header("Location: " . BASE_URL . "guru/dashboard_guru.php");
            exit();
        case 'siswa':
            header("Location: " . BASE_URL . "siswa/dashboard_siswa.php");
            exit();
    }
}

// Jika BELUM login, alihkan ke form login
header("Location: " . BASE_URL . "auth/form_login.php");
exit();
?>