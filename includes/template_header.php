<?php
// Pastikan session sudah aktif.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_role     = $_SESSION['role'] ?? 'guest';
$current_username = $_SESSION['username'] ?? 'Pengguna';
$current_page     = basename($_SERVER['PHP_SELF'] ?? '');

// Ambil foto profil pengguna yang sedang login.
$current_foto = null;
if (isset($_SESSION['user_id'], $conn)) {
    $user_id_header = (int) $_SESSION['user_id'];
    $stmt_foto = mysqli_prepare($conn, 'SELECT foto FROM users WHERE id = ? LIMIT 1');

    if ($stmt_foto) {
        mysqli_stmt_bind_param($stmt_foto, 'i', $user_id_header);
        mysqli_stmt_execute($stmt_foto);
        $result_foto = mysqli_stmt_get_result($stmt_foto);

        if ($result_foto && ($data_foto = mysqli_fetch_assoc($result_foto))) {
            $current_foto = $data_foto['foto'] ?? null;
        }

        mysqli_stmt_close($stmt_foto);
    }
}

$profile_image_path = __DIR__ . '/../assets/img/profil/' . basename((string) $current_foto);
$has_profile_image = !empty($current_foto) && is_file($profile_image_path);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e3a8a">
    <title>SIPN - Sistem Informasi Pengelolaan Nilai | SMK Sangkuriang 1 Cimahi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
</head>
<body data-theme="light">

<div class="app-layout-wrapper">
    <!-- Navigasi utama -->
    <aside class="app-sidebar" id="appSidebar" aria-label="Navigasi utama" aria-hidden="false">
        <div class="sidebar-main-content">
            <div class="sidebar-brand-box">
                <div class="sidebar-logo-icon" aria-hidden="true">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span class="brand-title">SIPN AKADEMIK</span>
                    <span class="brand-subtitle">SMK SANGKURIANG 1</span>
                </div>
            </div>

            <nav class="sidebar-menu-container" aria-label="Menu aplikasi">
                <?php if ($current_role === 'siswa' || $current_role === 'guru'): ?>
                    <div class="sidebar-mobile-role-navigation">
                        <span class="sidebar-section-title">Navigasi Akademik</span>
                        <ul class="sidebar-nav-list">
                            <?php if ($current_role === 'siswa'): ?>
                                <li>
                                    <a href="<?= BASE_URL; ?>siswa/dashboard_siswa.php"
                                       class="nav-link <?= $current_page === 'dashboard_siswa.php' ? 'active' : ''; ?>"
                                       title="Beranda">
                                        <i class="bi bi-house-door" aria-hidden="true"></i><span>Beranda</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php"
                                       class="nav-link <?= $current_page === 'lihat_nilai_rapor.php' ? 'active' : ''; ?>"
                                       title="Lihat Nilai Rapor">
                                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i><span>Lihat Nilai Rapor</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a href="<?= BASE_URL; ?>guru/dashboard_guru.php"
                                       class="nav-link <?= $current_page === 'dashboard_guru.php' ? 'active' : ''; ?>"
                                       title="Beranda">
                                        <i class="bi bi-house-door" aria-hidden="true"></i><span>Beranda</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php"
                                       class="nav-link <?= strpos($current_page, 'nilai') !== false ? 'active' : ''; ?>"
                                       title="Kelola Nilai Siswa">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i><span>Kelola Nilai Siswa</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <span class="sidebar-section-title">Menu Utilitas</span>
                <ul class="sidebar-nav-list">
                    <?php if ($current_role === 'admin'): ?>
                        <li>
                            <a href="<?= BASE_URL; ?>admin/dashboard_admin.php"
                               class="nav-link <?= $current_page === 'dashboard_admin.php' ? 'active' : ''; ?>"
                               title="Dashboard">
                                <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>admin/riwayat.php"
                               class="nav-link <?= $current_page === 'riwayat.php' ? 'active' : ''; ?>"
                               title="Riwayat Aktivitas">
                                <i class="bi bi-clock-history" aria-hidden="true"></i><span>Riwayat Aktivitas</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>admin/data_guru.php"
                               class="nav-link <?= strpos($current_page, 'guru') !== false ? 'active' : ''; ?>"
                               title="Data Guru">
                                <i class="bi bi-person-workspace" aria-hidden="true"></i><span>Data Guru</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>admin/data_siswa.php"
                               class="nav-link <?= strpos($current_page, 'siswa') !== false ? 'active' : ''; ?>"
                               title="Data Siswa">
                                <i class="bi bi-people-fill" aria-hidden="true"></i><span>Data Siswa</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>admin/data_mapel.php"
                               class="nav-link <?= strpos($current_page, 'mapel') !== false ? 'active' : ''; ?>"
                               title="Mata Pelajaran">
                                <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i><span>Mata Pelajaran</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>admin/data_mengajar.php"
                               class="nav-link <?= strpos($current_page, 'mengajar') !== false ? 'active' : ''; ?>"
                               title="Penugasan">
                                <i class="bi bi-link-45deg" aria-hidden="true"></i><span>Penugasan</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>admin/data_user.php"
                               class="nav-link <?= strpos($current_page, 'user') !== false ? 'active' : ''; ?>"
                               title="Kelola User">
                                <i class="bi bi-person-badge-fill" aria-hidden="true"></i><span>Kelola User</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <button type="button" id="themeToggleSidebar" class="nav-link sidebar-theme-toggle-btn">
                            <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
                            <span id="themeText">Dark Mode</span>
                        </button>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>about.php"
                           class="nav-link <?= $current_page === 'about.php' ? 'active' : ''; ?>"
                           title="Tentang SIPN">
                            <i class="bi bi-info-circle" aria-hidden="true"></i><span>Tentang SIPN</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>contact.php"
                           class="nav-link <?= $current_page === 'contact.php' ? 'active' : ''; ?>"
                           title="Kontak Bantuan">
                            <i class="bi bi-headset" aria-hidden="true"></i><span>Kontak Bantuan</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="sidebar-profile-section">
            <div id="profileDropdown" class="profile-dropdown-menu">
                <a href="<?= BASE_URL; ?>profile.php" class="dropdown-link">
                    <i class="bi bi-gear-fill" aria-hidden="true"></i><span>Pengaturan Profil</span>
                </a>
                <a href="<?= BASE_URL; ?>auth/logout.php" class="dropdown-link dropdown-link-danger">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i><span>Keluar Sistem</span>
                </a>
            </div>

            <button type="button"
                    id="profileTriggerBtn"
                    class="profile-trigger-card"
                    aria-expanded="false"
                    aria-controls="profileDropdown"
                    aria-label="Menu profil pengguna">
                <span class="profile-avatar-box" aria-hidden="true">
                    <?php if ($has_profile_image): ?>
                        <img src="<?= BASE_URL; ?>assets/img/profil/<?= rawurlencode(basename($current_foto)); ?>" alt="">
                    <?php else: ?>
                        <?= htmlspecialchars(strtoupper(substr($current_username, 0, 1)), ENT_QUOTES, 'UTF-8'); ?>
                    <?php endif; ?>
                </span>
                <span class="profile-meta">
                    <span class="profile-username"><?= htmlspecialchars($current_username, ENT_QUOTES, 'UTF-8'); ?></span>
                    
                <i class="bi bi-chevron-up profile-arrow" aria-hidden="true"></i>
            </button>
        </div>
    </aside>

    <!-- Backdrop khusus navigasi mobile -->
    <button type="button"
            class="sidebar-backdrop"
            id="sidebarBackdrop"
            aria-label="Tutup menu navigasi"
            aria-hidden="true"
            tabindex="-1"></button>

    <div class="app-main-content" id="appMainContent">
        <header class="app-topbar-header">
            <div class="topbar-leading">
                <button type="button"
                        class="sidebar-toggle"
                        id="sidebarToggle"
                        aria-controls="appSidebar"
                        aria-expanded="false"
                        aria-label="Buka menu navigasi">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>

                <div class="topbar-left-brand">
                    <span class="topbar-portal-title">Sistem Informasi Pengelolaan Nilai</span>
                    <span class="topbar-brand-caption">SMK SANGKURIANG 1 CIMAHI</span>
                </div>
            </div>

            <nav class="topbar-navigation" aria-label="Navigasi cepat">
                <ul>
                    <?php if ($current_role === 'siswa'): ?>
                        <li><a href="<?= BASE_URL; ?>siswa/dashboard_siswa.php" class="topbar-nav-link <?= $current_page === 'dashboard_siswa.php' ? 'active' : ''; ?>"><i class="bi bi-house-door" aria-hidden="true"></i><span>Beranda</span></a></li>
                        <li><a href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php" class="topbar-nav-link <?= $current_page === 'lihat_nilai_rapor.php' ? 'active' : ''; ?>"><i class="bi bi-file-earmark-text" aria-hidden="true"></i><span>Lihat Nilai Rapor</span></a></li>
                    <?php elseif ($current_role === 'guru'): ?>
                        <li><a href="<?= BASE_URL; ?>guru/dashboard_guru.php" class="topbar-nav-link <?= $current_page === 'dashboard_guru.php' ? 'active' : ''; ?>"><i class="bi bi-house-door" aria-hidden="true"></i><span>Beranda</span></a></li>
                        <li><a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php" class="topbar-nav-link <?= strpos($current_page, 'nilai') !== false ? 'active' : ''; ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i><span>Kelola Nilai Siswa</span></a></li>
                    <?php elseif ($current_role === 'admin'): ?>
                        <li><a href="<?= BASE_URL; ?>admin/dashboard_admin.php" class="topbar-nav-link <?= $current_page === 'dashboard_admin.php' ? 'active' : ''; ?>"><i class="bi bi-house-door" aria-hidden="true"></i><span>Beranda</span></a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="topbar-role-indicator" aria-label="Peran akun saat ini">
                <span class="topbar-role-dot" aria-hidden="true"></span>
                <span><?= htmlspecialchars($current_role === 'admin' ? 'Administrator' : ($current_role === 'guru' ? 'Guru' : ($current_role === 'siswa' ? 'Siswa' : 'Tamu')), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </header>

        <main class="page-container" id="mainContent">
            <?php display_flash_message(); ?>