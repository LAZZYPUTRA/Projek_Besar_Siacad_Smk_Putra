<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['admin']);

$total_siswa = 0;
$q_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
if ($q_siswa) {
    $row_siswa = mysqli_fetch_assoc($q_siswa);
    $total_siswa = $row_siswa['total'];
}

$total_guru = 0;
$q_guru = mysqli_query($conn, "SELECT COUNT(*) as total FROM guru");
if ($q_guru) {
    $row_guru = mysqli_fetch_assoc($q_guru);
    $total_guru = $row_guru['total'];
}

$total_mapel = 0;
$q_mapel = mysqli_query($conn, "SELECT COUNT(*) as total FROM mapel");
if ($q_mapel) {
    $row_mapel = mysqli_fetch_assoc($q_mapel);
    $total_mapel = $row_mapel['total'];
}

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="page-header">
    <h2><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>
</div>

<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-content">
            <span class="stat-title">Total Siswa</span>
            <span class="stat-value"><?= $total_siswa; ?></span>
        </div>
        <i class="bi bi-people-fill stat-icon"></i>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-content">
            <span class="stat-title">Total Guru</span>
            <span class="stat-value"><?= $total_guru; ?></span>
        </div>
        <i class="bi bi-person-badge-fill stat-icon"></i>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-content">
            <span class="stat-title">Total Mata Pelajaran</span>
            <span class="stat-value"><?= $total_mapel; ?></span>
        </div>
        <i class="bi bi-book-fill stat-icon"></i>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>