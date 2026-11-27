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
                <img src="<?= BASE_URL; ?>assets/img/profil/<?= rawurlencode(basename((string) $current_foto)); ?>" alt="">
            <?php else: ?>
                <?= htmlspecialchars(strtoupper(substr($current_username, 0, 1)), ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </span>
        <span class="profile-meta">
            <span class="profile-username"><?= htmlspecialchars($current_username, ENT_QUOTES, 'UTF-8'); ?></span>
        </span>
        <i class="bi bi-chevron-up profile-arrow" aria-hidden="true"></i>
    </button>
</div>
