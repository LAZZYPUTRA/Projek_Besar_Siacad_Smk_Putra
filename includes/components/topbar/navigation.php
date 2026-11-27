<nav class="topbar-navigation" aria-label="Navigasi cepat">
    <ul>
        <?php foreach ($topbar_navigation_items as $item): ?>
            <li>
                <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="topbar-nav-link <?= $current_page === $item['page'] ? 'active' : ''; ?>">
                    <i class="bi <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
