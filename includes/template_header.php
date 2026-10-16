<?php
// Pastikan session sudah aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_role     = $_SESSION['role'] ?? 'guest';
$current_username = $_SESSION['username'] ?? 'Pengguna';
$current_page     = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPN - Sistem Informasi Pengelolaan Nilai | SMK Sangkuriang 1 Cimahi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
</head>
<body data-theme="light">

<!-- Layout Utama Enterprise (Sidebar Kiri + Konten & Topbar Kanan) -->
<div class="app-layout-wrapper">
    
    <!-- ================= SIDEBAR KIRI ================= -->
    <aside class="app-sidebar">
        <div>
            <!-- Logo Sekolah / Brand -->
            <div class="sidebar-brand-box">
                <div class="sidebar-logo-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span class="brand-title">SIPN AKADEMIK</span>
                    <span class="brand-subtitle">SMK SANGKURIANG 1</span>
                </div>
            </div>

            <!-- Menu Samping (Navigasi & Utilitas) -->
            <div class="sidebar-menu-container">
                <span class="sidebar-section-title">Menu Utilitas</span>
                <ul class="sidebar-nav-list">
                    <?php if ($current_role === 'admin'): ?>
                        <li><a href="<?= BASE_URL; ?>admin/dashboard_admin.php" class="nav-link <?= $current_page === 'dashboard_admin.php' ? 'active' : ''; ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
                        <li><a href="<?= BASE_URL; ?>admin/riwayat.php" class="nav-link <?= $current_page === 'riwayat.php' ? 'active' : ''; ?>"><i class="bi bi-clock-history"></i> Riwayat Aktivitas</a></li>
                        <li><a href="<?= BASE_URL; ?>admin/data_guru.php" class="nav-link <?= strpos($current_page, 'guru') !== false ? 'active' : ''; ?>"><i class="bi bi-person-workspace"></i> Data Guru</a></li>
                        <li><a href="<?= BASE_URL; ?>admin/data_siswa.php" class="nav-link <?= strpos($current_page, 'siswa') !== false ? 'active' : ''; ?>"><i class="bi bi-people-fill"></i> Data Siswa</a></li>
                        <li><a href="<?= BASE_URL; ?>admin/data_mapel.php" class="nav-link <?= strpos($current_page, 'mapel') !== false ? 'active' : ''; ?>"><i class="bi bi-journal-bookmark-fill"></i> Mata Pelajaran</a></li>
                        <li><a href="<?= BASE_URL; ?>admin/data_mengajar.php" class="nav-link <?= strpos($current_page, 'mengajar') !== false ? 'active' : ''; ?>"><i class="bi bi-link-45deg"></i> Penugasan</a></li>
                        <li><a href="<?= BASE_URL; ?>admin/data_user.php" class="nav-link <?= strpos($current_page, 'user') !== false ? 'active' : ''; ?>"><i class="bi bi-person-badge-fill"></i> Kelola User</a></li>
                    <?php endif; ?>
                    
                    <li>
                        <button type="button" id="themeToggleSidebar" class="nav-link sidebar-theme-toggle-btn">
                            <i class="bi bi-moon-stars-fill"></i> <span id="themeText">Dark Mode</span>
                        </button>
                    </li>
                    <li><a href="<?= BASE_URL; ?>about.php" class="nav-link <?= $current_page === 'about.php' ? 'active' : ''; ?>"><i class="bi bi-info-circle"></i> Tentang SIPN</a></li>
                    <li><a href="<?= BASE_URL; ?>contact.php" class="nav-link <?= $current_page === 'contact.php' ? 'active' : ''; ?>"><i class="bi bi-headset"></i> Kontak Bantuan</a></li>
                </ul>
            </div>
        </div>

        <!-- ================= PROFIL DI BAGIAN BAWAH SIDEBAR ================= -->
        <div class="sidebar-profile-section">
            
            <!-- Popover Menu Setting & Logout -->
            <div id="profileDropdown" class="profile-dropdown-menu">
                <a href="<?= BASE_URL; ?>profile.php" class="dropdown-link">
                    <i class="bi bi-gear-fill text-primary"></i> Pengaturan Profil
                </a>
                <a href="<?= BASE_URL; ?>auth/logout.php" class="dropdown-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Keluar Sistem
                </a>
            </div>

            <!-- Tombol Profil (Trigger Popover) -->
            <div id="profileTriggerBtn" class="profile-trigger-card">
                <div class="profile-avatar-box">
                    <?= strtoupper(substr($current_username, 0, 1)); ?>
                </div>
                <div class="profile-meta">
                    <div class="profile-username"><?= htmlspecialchars($current_username); ?></div>
                    <div class="profile-userrole"><?= $current_role; ?></div>
                </div>
                <i class="bi bi-chevron-up profile-arrow"></i>
            </div>
        </div>
    </aside>

    <!-- ================= KONTEN UTAMA & TOPBAR ================= -->
    <div class="app-main-content">
        
        <!-- Navbar Atas (Beranda & Halaman Nilai) -->
        <header class="app-topbar-header">
            <div class="topbar-left-brand">
                <span class="topbar-portal-title">Sistem Informasi Pengelolaan Nilai</span>
            </div>
            <nav class="topbar-navigation">
                <ul>
                    <?php if ($current_role === 'siswa'): ?>
                        <li><a href="<?= BASE_URL; ?>siswa/dashboard_siswa.php" class="topbar-nav-link <?= $current_page === 'dashboard_siswa.php' ? 'active' : ''; ?>"><i class="bi bi-house-door"></i> Beranda</a></li>
                        <li><a href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php" class="topbar-nav-link <?= $current_page === 'lihat_nilai_rapor.php' ? 'active' : ''; ?>"><i class="bi bi-file-earmark-text"></i> Lihat Nilai Rapor</a></li>
                    <?php elseif ($current_role === 'guru'): ?>
                        <li><a href="<?= BASE_URL; ?>guru/dashboard_guru.php" class="topbar-nav-link <?= $current_page === 'dashboard_guru.php' ? 'active' : ''; ?>"><i class="bi bi-house-door"></i> Beranda</a></li>
                        <li><a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php" class="topbar-nav-link <?= strpos($current_page, 'nilai') !== false ? 'active' : ''; ?>"><i class="bi bi-pencil-square"></i> Kelola Nilai Siswa</a></li>
                    <?php elseif ($current_role === 'admin'): ?>
                        <li><a href="<?= BASE_URL; ?>admin/dashboard_admin.php" class="topbar-nav-link <?= $current_page === 'dashboard_admin.php' ? 'active' : ''; ?>"><i class="bi bi-house-door"></i> Beranda</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <!-- Area Konten Utama Halaman -->
        <main class="page-container">