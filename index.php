<?php
session_start();
require_once __DIR__ . '/config/koneksi_database.php';

// Jika pengguna SUDAH login, langsung lempar ke dashboard masing-masing
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: " . BASE_URL . "admin/dashboard_admin.php");
            exit;
        case 'guru':
            header("Location: " . BASE_URL . "guru/dashboard_guru.php");
            exit;
        case 'siswa':
            header("Location: " . BASE_URL . "siswa/dashboard_siswa.php");
            exit;
    }
}

// Jika BELUM login, tampilkan form login
require_once __DIR__ . '/auth/form_login.php';
?>