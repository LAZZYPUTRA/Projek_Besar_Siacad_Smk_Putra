<?php foreach ($role_navigation_items as $item): ?>
    <li>
        <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>"
           class="nav-link <?= $current_page === $item['page'] ? 'active' : ''; ?>"
           title="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
            <i class="bi <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
            <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
        </a>
    </li>
<?php endforeach; ?>
