<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/helper_fungsi.php';

/**
 * Memastikan pengguna sudah melakukan login
 */
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        set_flash_message('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        header("Location: " . BASE_URL . "auth/form_login.php");
        exit();
    }
}

/**
 * Memastikan role pengguna sesuai dengan halaman yang diakses
 * Contoh penggunaan: check_role(['admin']);
 */
function check_role($allowed_roles = []) {
    check_login();
    
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        $role_sekarang = $_SESSION['role'];
        set_flash_message('danger', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        header("Location: " . BASE_URL . $role_sekarang . "/dashboard_" . $role_sekarang . ".php");
        exit();
    }
}
?>
