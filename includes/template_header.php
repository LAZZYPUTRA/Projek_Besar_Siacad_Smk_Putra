<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/helper_fungsi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPN - Sistem Informasi Pengelolaan Nilai</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<?php if (isset($_SESSION['user_id'])): ?>
<!-- NAVBAR UTAMA -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL; ?>">
      <i class="bi bi-journal-check me-2"></i>SIPN SMK
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSIPN" aria-controls="navbarSIPN" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSIPN">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <!-- Menu Admin -->
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>admin/dashboard_admin.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>admin/data_user.php"><i class="bi bi-people me-1"></i> Data User</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>admin/data_siswa.php"><i class="bi bi-person-badge me-1"></i> Data Siswa</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>admin/data_guru.php"><i class="bi bi-person-workspace me-1"></i> Data Guru</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>admin/data_mapel.php"><i class="bi bi-book me-1"></i> Data Mapel</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>admin/data_mengajar.php"><i class="bi bi-card-checklist me-1"></i> Data Mengajar</a>
            </li>

        <?php elseif ($_SESSION['role'] === 'guru'): ?>
            <!-- Menu Guru -->
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>guru/dashboard_guru.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>guru/data_nilai_siswa.php"><i class="bi bi-pencil-square me-1"></i> Kelola Nilai</a>
            </li>

        <?php elseif ($_SESSION['role'] === 'siswa'): ?>
            <!-- Menu Siswa -->
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>siswa/dashboard_siswa.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php"><i class="bi bi-file-earmark-text me-1"></i> Lihat Nilai</a>
            </li>
        <?php endif; ?>
      </ul>

      <!-- User Information Dropdown -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['username']); ?> (<?= ucfirst($_SESSION['role']); ?>)
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
            <li>
              <a class="dropdown-item" href="<?= BASE_URL . $_SESSION['role']; ?>/profile_<?= $_SESSION['role']; ?>.php">
                <i class="bi bi-person me-2"></i> Kelola Profile
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="<?= BASE_URL; ?>auth/proses_logout.php">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<main class="container my-4 flex-grow-1">
<?php display_flash_message(); ?>