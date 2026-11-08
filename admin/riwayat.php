<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['admin']);

$escape = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

// Halaman ini mengikuti sumber data yang tersedia pada proyek: akun terbaru
// dari tabel users, diurutkan berdasarkan ID. Ini bukan audit log aktivitas.
$summary_query = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END), 0) AS total_admin,
        COALESCE(SUM(CASE WHEN role = 'guru' THEN 1 ELSE 0 END), 0) AS total_guru,
        COALESCE(SUM(CASE WHEN role = 'siswa' THEN 1 ELSE 0 END), 0) AS total_siswa
    FROM users
");

$summary = $summary_query ? mysqli_fetch_assoc($summary_query) : null;

$query_riwayat = mysqli_query(
    $conn,
    'SELECT id, username, role FROM users ORDER BY id DESC LIMIT 50'
);

$riwayat = [];
if ($query_riwayat) {
    while ($row = mysqli_fetch_assoc($query_riwayat)) {
        $riwayat[] = $row;
    }
}

$query_error = !$summary_query || !$query_riwayat;
$total_ditampilkan = count($riwayat);

$role_config = [
    'admin' => [
        'label' => 'Administrator',
        'class' => 'is-admin',
        'icon'  => 'bi-shield-lock-fill',
    ],
    'guru' => [
        'label' => 'Guru',
        'class' => 'is-guru',
        'icon'  => 'bi-person-workspace',
    ],
    'siswa' => [
        'label' => 'Siswa',
        'class' => 'is-siswa',
        'icon'  => 'bi-mortarboard-fill',
    ],
];

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="sipn-admin-data-page sipn-admin-data-page--history">
    <section class="sipn-admin-data-hero sipn-admin-data-enter" style="--admin-enter-delay: 0ms;" aria-labelledby="historyPageTitle">
        <div class="sipn-admin-data-hero__copy">
            <span class="sipn-admin-data-eyebrow">
                <i class="bi bi-clock-history" aria-hidden="true"></i>
                Pemantauan Sistem
            </span>
            <h1 id="historyPageTitle">Riwayat Pengguna</h1>
            <p>Pantau akun yang tercatat di SIPN melalui daftar pengguna terbaru, disusun berdasarkan urutan ID akun.</p>
            <div class="sipn-admin-data-hero__meta">
                <span><i class="bi bi-people-fill" aria-hidden="true"></i> Direktori akun pengguna</span>
                <span><i class="bi bi-shield-check" aria-hidden="true"></i> Panel administrator</span>
            </div>
        </div>
        <span class="sipn-admin-history-hero-mark" aria-hidden="true">
            <i class="bi bi-clock-history"></i>
        </span>
        <i class="bi bi-clock-history sipn-admin-data-hero__ornament" aria-hidden="true"></i>
    </section>

    <?php if ($query_error): ?>
        <div class="sipn-admin-data-notice" role="status">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>Sebagian data riwayat tidak berhasil dimuat. Periksa koneksi database dan tabel pengguna.</span>
        </div>
    <?php endif; ?>

    <div class="sipn-admin-history-note sipn-admin-data-enter" style="--admin-enter-delay: 45ms;" role="note">
        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
        <p><strong>Informasi sumber data:</strong> halaman ini menampilkan maksimal 50 akun terbaru dari tabel pengguna. Halaman ini belum merekam detail aktivitas seperti login, logout, edit, atau penghapusan data.</p>
    </div>

    <section class="sipn-admin-data-stats sipn-admin-data-enter" style="--admin-enter-delay: 85ms;" aria-label="Ringkasan akun pengguna">
        <article class="sipn-admin-data-stat">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--blue"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body">
                <small>Total Akun</small>
                <strong><?= $summary ? number_format((int) $summary['total'], 0, ',', '.') : '—'; ?></strong>
                <em>Seluruh akun terdaftar</em>
            </span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 125ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-history-stat-icon--admin"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body">
                <small>Administrator</small>
                <strong><?= $summary ? number_format((int) $summary['total_admin'], 0, ',', '.') : '—'; ?></strong>
                <em>Akun dengan akses admin</em>
            </span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 165ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--green"><i class="bi bi-person-workspace" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body">
                <small>Guru</small>
                <strong><?= $summary ? number_format((int) $summary['total_guru'], 0, ',', '.') : '—'; ?></strong>
                <em>Akun pendidik</em>
            </span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 205ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-history-stat-icon--student"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body">
                <small>Siswa</small>
                <strong><?= $summary ? number_format((int) $summary['total_siswa'], 0, ',', '.') : '—'; ?></strong>
                <em>Akun peserta didik</em>
            </span>
        </article>
    </section>

    <section class="sipn-admin-data-panel sipn-admin-data-enter" style="--admin-enter-delay: 245ms;" aria-labelledby="historyTableTitle">
        <div class="sipn-admin-data-panel__heading">
            <div>
                <span class="sipn-admin-data-kicker">Daftar terbaru</span>
                <h2 id="historyTableTitle">Akun yang Baru Tercatat</h2>
                <p>Gunakan pencarian untuk menemukan username, ID akun, atau jenis peran.</p>
            </div>
            <span class="sipn-admin-data-total">
                <i class="bi bi-list-ol" aria-hidden="true"></i>
                <?= number_format($total_ditampilkan, 0, ',', '.'); ?> dari maks. 50 akun
            </span>
        </div>

        <div class="sipn-admin-data-toolbar">
            <label class="sipn-admin-data-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" data-admin-table-search placeholder="Cari username, ID akun, atau role..." autocomplete="off" aria-label="Cari riwayat akun">
                <kbd>⌕</kbd>
            </label>
            <button type="button" class="sipn-admin-data-reset" data-admin-search-clear>
                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset
            </button>
            <span class="sipn-admin-data-result" aria-live="polite">
                Menampilkan <strong data-admin-results-count><?= $total_ditampilkan; ?></strong> dari <?= $total_ditampilkan; ?> akun
            </span>
        </div>

        <div class="sipn-admin-data-table-wrap sipn-admin-history-table-wrap">
            <table class="sipn-admin-data-table sipn-admin-history-table" data-admin-search-table>
                <thead>
                    <tr>
                        <th class="sipn-admin-data-col-number">No.</th>
                        <th>ID Akun</th>
                        <th>Username</th>
                        <th>Peran / Hak Akses</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($riwayat): ?>
                        <?php foreach ($riwayat as $index => $row): ?>
                            <?php
                                $row_id = (int) $row['id'];
                                $row_username = (string) $row['username'];
                                $row_role = strtolower((string) ($row['role'] ?? ''));
                                $role_info = $role_config[$row_role] ?? [
                                    'label' => ucfirst($row_role !== '' ? $row_role : 'Tidak diketahui'),
                                    'class' => 'is-unknown',
                                    'icon'  => 'bi-person-fill',
                                ];
                            ?>
                            <tr data-admin-search-row style="--admin-row-delay: <?= min($index, 12) * 45; ?>ms;">
                                <td class="sipn-admin-data-number"><?= $index + 1; ?></td>
                                <td><span class="sipn-admin-data-code">#<?= $row_id; ?></span></td>
                                <td>
                                    <div class="sipn-admin-data-person sipn-admin-history-person">
                                        <span class="sipn-admin-data-avatar <?= $role_info['class']; ?>" aria-hidden="true">
                                            <i class="bi <?= $role_info['icon']; ?>"></i>
                                        </span>
                                        <strong><?= $escape($row_username); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="sipn-admin-history-role <?= $role_info['class']; ?>">
                                        <?= $escape($role_info['label']); ?>
                                    </span>
                                </td>
                                <td><span class="sipn-admin-history-description"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Akun tercatat di SIPN</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php elseif (!$query_riwayat): ?>
                        <tr>
                            <td colspan="5" class="sipn-admin-data-empty">
                                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                                <strong>Riwayat gagal dimuat</strong>
                                <span>Periksa koneksi database, lalu muat ulang halaman.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="sipn-admin-data-empty">
                                <i class="bi bi-clock-history" aria-hidden="true"></i>
                                <strong>Belum ada akun yang tercatat</strong>
                                <span>Daftar akun akan tampil di bagian ini setelah tersedia.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="sipn-admin-data-no-match" data-admin-search-empty hidden>
                <i class="bi bi-search" aria-hidden="true"></i>
                <strong>Data tidak ditemukan</strong>
                <span>Coba kata kunci lain atau tekan tombol reset.</span>
            </div>
        </div>
        <div class="sipn-admin-history-footer">
            <span><i class="bi bi-shield-check" aria-hidden="true"></i> Informasi akun diambil langsung dari database SIPN.</span>
            <span>Urutan: ID terbesar ke terkecil</span>
        </div>
    </section>
</div>

<script src="<?= BASE_URL; ?>assets/js/admin-table-search.js?v=admin-history-20261108" defer></script>
<script>
// Putar ulang animasi jika halaman dipulihkan melalui tombol Back/Forward browser.
window.addEventListener('pageshow', function (event) {
    if (!event.persisted) return;

    var page = document.querySelector('.sipn-admin-data-page--history');
    if (!page || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var animated = page.querySelectorAll('.sipn-admin-data-enter, .sipn-admin-history-table tbody tr[data-admin-search-row]');
    animated.forEach(function (element) {
        element.style.animation = 'none';
    });
    void page.offsetHeight;
    animated.forEach(function (element) {
        element.style.removeProperty('animation');
    });
});
</script>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>