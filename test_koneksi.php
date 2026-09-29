<?php
require_once __DIR__ . '/config/koneksi_database.php';
require_once __DIR__ . '/includes/helper_fungsi.php';

echo "<h3 style='color:green;'>✓ Koneksi Database Berhasil!</h3>";
echo "Base URL: " . BASE_URL . "<br>";
echo "Status Session: " . (session_status() === PHP_SESSION_ACTIVE ? "Aktif" : "Tidak Aktif") . "<br>";

// Test Sanitasi Input
$input_kotor = "<script>alert('test')</script>";
$input_bersih = sanitize($input_kotor);
echo "Pengujian Sanitasi Input Anti-XSS: " . htmlspecialchars($input_bersih);
?>