<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';

$session_name = session_name();

// Hapus seluruh data autentikasi dari session yang sedang aktif.
$_SESSION = [];

// Hapus cookie session lama dari browser.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        $session_name,
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Akhiri session autentikasi lama.
session_destroy();

// Buat session anonim baru agar flash message tidak memakai ID session lama.
if (isset($_COOKIE[$session_name])) {
    unset($_COOKIE[$session_name]);
}
session_start();
session_regenerate_id(true);
set_flash_message('success', 'Anda berhasil logout dari SIPN. Sampai jumpa!');

header('Location: ' . BASE_URL . 'auth/form_login.php');
exit();