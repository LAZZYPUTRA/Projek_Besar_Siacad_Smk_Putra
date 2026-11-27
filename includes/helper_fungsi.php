<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Membersihkan input form dari karakter berbahaya (Anti-XSS & Trim)
 */
function sanitize($data) {
    if (is_null($data)) return '';
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Menyiapkan pesan notifikasi temporary (flash message)
 * $tipe: 'success', 'danger', 'warning', 'info'
 */
function set_flash_message($tipe, $pesan) {
    $_SESSION['flash_message'] = [
        'tipe' => $tipe,
        'pesan' => $pesan
    ];
}

/**
 * Menampilkan pesan notifikasi dalam bentuk Alert Bootstrap 5
 */
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $tipe = $_SESSION['flash_message']['tipe'];
        $pesan = $_SESSION['flash_message']['pesan'];
        
        echo '<div class="alert alert-' . $tipe . ' alert-dismissible fade show shadow-sm" role="alert">';
        echo '<i class="bi bi-info-circle-fill me-2"></i>' . $pesan;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message']);
    }
}
?>
