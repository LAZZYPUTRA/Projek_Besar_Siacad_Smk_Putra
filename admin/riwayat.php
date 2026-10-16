<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['admin']);

// Contoh pengambilan data riwayat jika Anda memiliki tabel log/riwayat di database.
// Jika belum ada tabel khusus log, Anda bisa menyesuaikan query-nya atau menampilkan data user terbaru.
$query_riwayat = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC LIMIT 50");

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="dashboard-wrapper" style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <i class="bi bi-clock-history text-primary"></i> Riwayat Aktivitas & Pengelolaan Sistem
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Log rekam jejak aktivitas operasional administrator dan penambahan data akun di dalam sistem.</p>
    </div>

    <div class="card-panel-clean" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; padding: 1.5rem;">
        <div class="table-responsive">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                        <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">No</th>
                        <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Username</th>
                        <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Peran (Role)</th>
                        <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Status / Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_riwayat) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_riwayat)): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem; font-weight: 600; color: var(--text-muted);"><?= $no++; ?></td>
                                <td style="padding: 0.85rem; font-weight: 700; color: var(--text-dark);"><?= htmlspecialchars($row['username']); ?></td>
                                <td style="padding: 0.85rem;">
                                    <span class="badge" style="background: rgba(30, 58, 138, 0.1); color: var(--primary-dark); padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                                        <?= htmlspecialchars($row['role']); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.85rem; color: var(--text-muted); font-size: 0.85rem;">Akun terdaftar di dalam database sistem.</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4" style="padding: 2rem; text-align: center; color: var(--text-muted);">Belum ada riwayat aktivitas tercatat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>