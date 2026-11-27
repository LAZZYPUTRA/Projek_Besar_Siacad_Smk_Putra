<?php
/**
 * View halaman dashboard_admin.
 * Variabel disiapkan oleh controller dashboard_admin.php.
 * File ini sengaja tidak memuat koneksi, otorisasi, atau layout global.
 */
?>
<div class="admin-dashboard">
    <section class="admin-dashboard__hero" aria-labelledby="adminDashboardTitle">
        <div class="admin-dashboard__hero-content">
            <span class="admin-dashboard__eyebrow">
                <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i>
                Sistem Informasi Akademik
            </span>
            <h1 id="adminDashboardTitle">Dashboard Administrator</h1>
            <p>Kelola data akademik sekolah melalui satu pusat kontrol.</p>
        </div>

        <div class="admin-dashboard__hero-meta" aria-label="Area pengguna">
            <span class="admin-dashboard__role-icon">
                <i class="bi bi-shield-check" aria-hidden="true"></i>
            </span>
            <span>
                <strong>Panel Admin</strong>
                <small>SMK Sangkuriang 1 Cimahi</small>
            </span>
        </div>
    </section>

    <?php if ($ada_gagal_query): ?>
        <div class="admin-dashboard__notice" role="status">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>Sebagian data statistik gagal dimuat. Periksa koneksi database atau struktur tabel terkait.</span>
        </div>
    <?php endif; ?>

    <section class="admin-dashboard__stats" aria-label="Ringkasan data sekolah">
        <a class="admin-stat-card admin-stat-card--students" href="<?= BASE_URL; ?>admin/data_siswa.php">
            <span class="admin-stat-card__label">Total Siswa</span>
            <span class="admin-stat-card__main">
                <strong class="admin-stat-card__value">
                    <?= $total_siswa !== null ? number_format($total_siswa, 0, ',', '.') : '—'; ?>
                </strong>
                <span class="admin-stat-card__icon" aria-hidden="true">
                    <i class="bi bi-people-fill"></i>
                </span>
            </span>
            <span class="admin-stat-card__footer">
                <span>Kelola data peserta didik</span>
                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
            </span>
        </a>

        <a class="admin-stat-card admin-stat-card--teachers" href="<?= BASE_URL; ?>admin/data_guru.php">
            <span class="admin-stat-card__label">Total Guru</span>
            <span class="admin-stat-card__main">
                <strong class="admin-stat-card__value">
                    <?= $total_guru !== null ? number_format($total_guru, 0, ',', '.') : '—'; ?>
                </strong>
                <span class="admin-stat-card__icon" aria-hidden="true">
                    <i class="bi bi-person-badge-fill"></i>
                </span>
            </span>
            <span class="admin-stat-card__footer">
                <span>Kelola data pendidik</span>
                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
            </span>
        </a>

        <a class="admin-stat-card admin-stat-card--subjects" href="<?= BASE_URL; ?>admin/data_mapel.php">
            <span class="admin-stat-card__label">Mata Pelajaran</span>
            <span class="admin-stat-card__main">
                <strong class="admin-stat-card__value">
                    <?= $total_mapel !== null ? number_format($total_mapel, 0, ',', '.') : '—'; ?>
                </strong>
                <span class="admin-stat-card__icon" aria-hidden="true">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </span>
            </span>
            <span class="admin-stat-card__footer">
                <span>Kelola kurikulum mapel</span>
                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
            </span>
        </a>
    </section>

    <section class="admin-dashboard__quick-section" aria-labelledby="adminQuickLinksTitle">
        <div class="admin-dashboard__section-heading">
            <div>
                <span class="admin-dashboard__section-kicker">Navigasi cepat</span>
                <h2 id="adminQuickLinksTitle">Akses Pengelolaan</h2>
                <p>Pilih modul yang ingin Anda kelola.</p>
            </div>
            <span class="admin-dashboard__section-mark" aria-hidden="true">
                <i class="bi bi-arrow-down-right-square"></i>
            </span>
        </div>

        <div class="admin-dashboard__quick-grid">
            <a class="admin-quick-link" href="<?= BASE_URL; ?>admin/data_siswa.php">
                <span class="admin-quick-link__icon admin-quick-link__icon--blue" aria-hidden="true">
                    <i class="bi bi-people"></i>
                </span>
                <span class="admin-quick-link__text">
                    <strong>Data Siswa</strong>
                    <small>Informasi dan akun peserta didik</small>
                </span>
                <i class="bi bi-chevron-right admin-quick-link__arrow" aria-hidden="true"></i>
            </a>

            <a class="admin-quick-link" href="<?= BASE_URL; ?>admin/data_guru.php">
                <span class="admin-quick-link__icon admin-quick-link__icon--green" aria-hidden="true">
                    <i class="bi bi-person-workspace"></i>
                </span>
                <span class="admin-quick-link__text">
                    <strong>Data Guru</strong>
                    <small>Informasi dan akun pendidik</small>
                </span>
                <i class="bi bi-chevron-right admin-quick-link__arrow" aria-hidden="true"></i>
            </a>

            <a class="admin-quick-link" href="<?= BASE_URL; ?>admin/data_mapel.php">
                <span class="admin-quick-link__icon admin-quick-link__icon--violet" aria-hidden="true">
                    <i class="bi bi-journal-bookmark"></i>
                </span>
                <span class="admin-quick-link__text">
                    <strong>Mata Pelajaran</strong>
                    <small>Pengaturan data mata pelajaran</small>
                </span>
                <i class="bi bi-chevron-right admin-quick-link__arrow" aria-hidden="true"></i>
            </a>

            <a class="admin-quick-link" href="<?= BASE_URL; ?>admin/data_mengajar.php">
                <span class="admin-quick-link__icon admin-quick-link__icon--orange" aria-hidden="true">
                    <i class="bi bi-diagram-3"></i>
                </span>
                <span class="admin-quick-link__text">
                    <strong>Penugasan</strong>
                    <small>Relasi siswa dan mata pelajaran</small>
                </span>
                <i class="bi bi-chevron-right admin-quick-link__arrow" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <p class="admin-dashboard__footnote">
        <i class="bi bi-info-circle" aria-hidden="true"></i>
        Statistik dihitung langsung dari data yang tersimpan di database.
    </p>
</div>
