<!-- Navigasi utama SIPN -->
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
            <?php if (!empty($role_navigation_items)): ?>
                <div class="sidebar-mobile-role-navigation">
                    <span class="sidebar-section-title">Navigasi Akademik</span>
                    <ul class="sidebar-nav-list">
                        <?php require __DIR__ . '/sidebar/role_navigation.php'; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <span class="sidebar-section-title">Menu Utilitas</span>
            <ul class="sidebar-nav-list">
                <?php require __DIR__ . '/sidebar/utility_navigation.php'; ?>
            </ul>
        </nav>
    </div>

    <?php require __DIR__ . '/sidebar/profile_menu.php'; ?>
</aside>

<?php require __DIR__ . '/sidebar/mobile_backdrop.php'; ?>
