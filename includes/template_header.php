<?php
// Pastikan session sudah aktif.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_role     = $_SESSION['role'] ?? 'guest';
$current_username = $_SESSION['username'] ?? 'Pengguna';
$current_page     = basename($_SERVER['PHP_SELF'] ?? '');

$is_dashboard_page = in_array($current_page, [
    'dashboard_admin.php',
    'dashboard_guru.php',
    'dashboard_siswa.php',
], true);
// Dashboard menggunakan staggered entrance setiap kali halaman beranda dibuka.
// Flag lama dari proses login tetap dikonsumsi agar tidak tertinggal di session.
$animate_dashboard_entry = $is_dashboard_page;
if ($is_dashboard_page && isset($_SESSION['sipn_dashboard_entry'])) {
    unset($_SESSION['sipn_dashboard_entry']);
}

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

// Satu sumber data navigasi untuk sidebar dan topbar.
require_once __DIR__ . '/components/navigation/role_links.php';

// Kerangka halaman dipisahkan ke partial agar mudah dirawat.
require_once __DIR__ . '/layouts/document_head.php';
?>
<div class="app-layout-wrapper">
    <?php require __DIR__ . '/components/sidebar.php'; ?>

    <div class="app-main-content" id="appMainContent">
        <?php require __DIR__ . '/components/topbar.php'; ?>

        <main class="page-container" id="mainContent">
            <?php display_flash_message(); ?>
