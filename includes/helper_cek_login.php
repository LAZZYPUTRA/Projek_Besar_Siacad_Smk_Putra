<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Memastikan pengguna sudah melakukan login
 */
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['flash_message'] = [
            'tipe' => 'warning',
            'pesan' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.'
        ];
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
        $_SESSION['flash_message'] = [
            'tipe' => 'danger',
            'pesan' => 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.'
        ];
        header("Location: " . BASE_URL . $role_sekarang . "/dashboard_" . $role_sekarang . ".php");
        exit();
    }
}
?>