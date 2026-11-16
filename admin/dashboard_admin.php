<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['admin']);

// Nilai awal NULL agar kegagalan query tidak disamakan dengan jumlah data 0.
$total_siswa = null;
$q_siswa = mysqli_query($conn, "SELECT COUNT(*) AS total FROM siswa");
if ($q_siswa) {
    $row_siswa = mysqli_fetch_assoc($q_siswa);
    $total_siswa = (int) ($row_siswa['total'] ?? 0);
}

$total_guru = null;
$q_guru = mysqli_query($conn, "SELECT COUNT(*) AS total FROM guru");
if ($q_guru) {
    $row_guru = mysqli_fetch_assoc($q_guru);
    $total_guru = (int) ($row_guru['total'] ?? 0);
}

$total_mapel = null;
$q_mapel = mysqli_query($conn, "SELECT COUNT(*) AS total FROM mapel");
if ($q_mapel) {
    $row_mapel = mysqli_fetch_assoc($q_mapel);
    $total_mapel = (int) ($row_mapel['total'] ?? 0);
}

$ada_gagal_query = ($total_siswa === null || $total_guru === null || $total_mapel === null);

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/dashboard_admin_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
