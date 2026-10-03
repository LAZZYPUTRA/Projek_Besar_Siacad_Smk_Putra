<?php
session_start();
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';

// Hapus semua variabel session
$_SESSION = array();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Mulai session baru untuk pesan notifikasi logout
session_start();
set_flash_message('info', 'Anda telah berhasil logout.');

header("Location: " . BASE_URL . "auth/form_login.php");
exit();
?>