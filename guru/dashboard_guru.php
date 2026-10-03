<?php
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['guru']); // Hanya Guru yang bisa mengakses

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="p-4 bg-white rounded shadow-sm">
    <h2><i class="bi bi-speedometer2 text-success me-2"></i>Dashboard Guru</h2>
    <p class="text-muted">Selamat datang di Panel Pengelolaan Nilai.</p>
    <div class="alert alert-info">
        Fitur input dan edit nilai siswa akan diimplementasikan pada Phase 4.
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>
