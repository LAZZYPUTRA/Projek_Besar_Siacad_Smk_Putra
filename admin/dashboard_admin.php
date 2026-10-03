<?php
// 1. Panggil koneksi database dan helper cek login di baris paling atas
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

// Pastikan hanya role admin yang bisa mengakses
check_role(['admin']);

// 2. Baris 6 (atau query pertama): Pastikan variabel $conn sesuai dengan yang ada di koneksi_database.php
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

<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
    <h2><i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard Admin</h2>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-1">Total Siswa</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_siswa; ?></h2>
                </div>
                <i class="bi bi-people-fill display-5 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-1">Total Guru</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_guru; ?></h2>
                </div>
                <i class="bi bi-person-badge-fill display-5 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white shadow-sm border-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-1">Total Mata Pelajaran</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_mapel; ?></h2>
                </div>
                <i class="bi bi-book-fill display-5 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>