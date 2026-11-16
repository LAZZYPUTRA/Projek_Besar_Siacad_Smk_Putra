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
