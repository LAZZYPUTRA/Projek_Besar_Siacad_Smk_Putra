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

    <?php require __DIR__ . '/topbar/navigation.php'; ?>
    <?php require __DIR__ . '/topbar/role_indicator.php'; ?>
</header>
