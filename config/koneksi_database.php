<?php
// Konfigurasi Database
define('BASE_URL', 'http://localhost:8000/');
$host     = "localhost";
$username = "putra";
$password = "password123";
$database = "db_siacad_smk_2";

// Membuat Koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Cek Koneksi
if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>