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
    if (!isset($_SESSION['flash_message']) || !is_array($_SESSION['flash_message'])) {
        return;
    }

    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Flash message hanya ditampilkan satu kali.

    $allowed_types = ['success', 'danger', 'warning', 'info'];
    $type = in_array($flash['tipe'] ?? '', $allowed_types, true)
        ? $flash['tipe']
        : 'info';
    $message = htmlspecialchars((string) ($flash['pesan'] ?? ''), ENT_QUOTES, 'UTF-8');

    $icons = [
        'success' => 'bi-check-circle-fill',
        'danger'  => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        'info'    => 'bi-info-circle-fill',
    ];

    echo '<div class="alert alert-' . $type . ' sipn-flash-alert" role="status" aria-live="polite">';
    echo '<span class="sipn-flash-alert__content">';
    echo '<i class="bi ' . $icons[$type] . '" aria-hidden="true"></i>';
    echo '<span>' . $message . '</span>';
    echo '</span>';
    echo '<button type="button" class="btn-close" aria-label="Tutup notifikasi" onclick="this.parentNode.remove()">&times;</button>';
    echo '</div>';
}
?>