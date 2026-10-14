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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
</head>
<body class="app-body">

<?php if (isset($_SESSION['role'])): ?>
<header class="app-header">
    <div class="header-container">
        <a class="brand-logo" href="<?= BASE_URL; ?>">
            <i class="bi bi-journal-check"></i> SIPN SMK
        </a>
        
        <nav class="main-nav">
            <ul class="nav-list">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>admin/dashboard_admin.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>admin/data_guru.php">Data Guru</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>admin/data_siswa.php">Data Siswa</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>admin/data_mapel.php">Mata Pelajaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>admin/data_mengajar.php">Penugasan</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>admin/data_user.php">Akun User</a></li>
                     <button type="button" id="themeToggle" class="btn btn-sm btn-secondary" title="Ganti Tema">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>

                <?php elseif ($_SESSION['role'] === 'guru'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>guru/dashboard_guru.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>guru/data_nilai_siswa.php">Kelola Nilai</a></li>
                <?php elseif ($_SESSION['role'] === 'siswa'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>siswa/dashboard_siswa.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php">Nilai Rapor</a></li>
                     <button type="button" id="themeToggle" class="btn btn-sm btn-secondary" title="Ganti Tema">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>

                <?php endif; ?>
            </ul>

            <div class="user-menu">
                <span class="user-name"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']); ?></span>
                <div class="dropdown-content">
                    <a href="<?= BASE_URL; ?>profile.php"><i class="bi bi-person"></i> Profil</a>
                    <a href="<?= BASE_URL; ?>auth/proses_logout.php" class="link-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
                     <button type="button" id="themeToggle" class="btn btn-sm btn-secondary" title="Ganti Tema">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>

                   
                </div>
            </div>
        </nav>
    </div>
</header>
<?php endif; ?>

<main class="page-container">
<?php display_flash_message(); ?>