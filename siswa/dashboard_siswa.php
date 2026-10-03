<?php
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['siswa']);

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="p-4 bg-white rounded shadow-sm">
    <h2><i class="bi bi-speedometer2 text-success me-2"></i>Dashboard Siswa</h2>
    <p class="text-muted">Selamat datang di Panel Siswa SIPN.</p>
    <div class="alert alert-info">
        Fitur untuk melihat nilai rapor Anda akan tersedia pada menu "Lihat Nilai".
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>