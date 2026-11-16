<?php
/**
 * View halaman data_user.
 * Variabel disiapkan oleh controller data_user.php.
 * File ini sengaja tidak memuat koneksi, otorisasi, atau layout global.
 */
?>
<div class="sipn-admin-data-page sipn-admin-data-page--user">
    <header class="sipn-admin-data-hero sipn-admin-data-enter" style="--admin-enter-delay: 0ms;">
        <div class="sipn-admin-data-hero__copy">
            <span class="sipn-admin-data-eyebrow"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Administrasi Akun</span>
            <h1>Kelola Akun User</h1>
            <p>Atur identitas login, hak akses, dan keamanan akun administrator, guru, serta peserta didik dalam satu halaman.</p>
            <div class="sipn-admin-data-hero__meta">
                <span><i class="bi bi-people-fill" aria-hidden="true"></i> <?= number_format($total_user, 0, ',', '.'); ?> akun terdaftar</span>
                <span><i class="bi bi-fingerprint" aria-hidden="true"></i> Pengelolaan akses SIPN</span>
            </div>
        </div>
        <span class="sipn-admin-data-hero__ornament" aria-hidden="true"><i class="bi bi-person-lock"></i></span>
    </header>

    <?php if ($data_user_error): ?>
        <div class="sipn-admin-data-notice" role="alert"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i><span>Data akun gagal dimuat. Periksa koneksi database, lalu muat ulang halaman.</span></div>
    <?php endif; ?>

    <section class="sipn-admin-data-stats" aria-label="Ringkasan akun pengguna">
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 65ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--blue"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Total Akun</small><strong><?= number_format($total_user, 0, ',', '.'); ?></strong><em>Seluruh akun di SIPN</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 115ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--red"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Administrator</small><strong><?= number_format($total_admin, 0, ',', '.'); ?></strong><em>Akun dengan akses admin</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 165ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--green"><i class="bi bi-person-workspace" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Guru</small><strong><?= number_format($total_guru, 0, ',', '.'); ?></strong><em>Akun pendidik</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 215ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--violet"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Siswa</small><strong><?= number_format($total_siswa, 0, ',', '.'); ?></strong><em>Akun peserta didik</em></span>
        </article>
    </section>

    <section class="sipn-admin-data-panel sipn-admin-data-enter" style="--admin-enter-delay: 255ms;" aria-labelledby="userTableTitle">
        <div class="sipn-admin-data-panel__heading">
            <div>
                <span class="sipn-admin-data-kicker">Direktori pengguna</span>
                <h2 id="userTableTitle">Daftar Akun Pengguna</h2>
                <p>Cari username atau role, lalu gunakan aksi untuk mengubah hak akses maupun mengatur ulang password.</p>
            </div>
            <span class="sipn-admin-data-total"><i class="bi bi-person-lines-fill" aria-hidden="true"></i> <?= number_format($total_user, 0, ',', '.'); ?> akun</span>
        </div>

        <div class="sipn-admin-data-toolbar">
            <label class="sipn-admin-data-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" data-admin-table-search placeholder="Cari username atau role..." autocomplete="off" aria-label="Cari akun berdasarkan username atau role">
                <kbd>⌕</kbd>
            </label>
            <button type="button" class="sipn-admin-data-reset" data-admin-search-clear><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset</button>
            <span class="sipn-admin-data-result" aria-live="polite">Menampilkan <strong data-admin-results-count><?= $total_user; ?></strong> dari <?= $total_user; ?> akun</span>
        </div>

        <div class="sipn-admin-data-table-wrap">
            <table class="sipn-admin-data-table" data-admin-search-table>
                <thead><tr>
                    <th class="sipn-admin-data-col-number">No.</th>
                    <th>Username</th>
                    <th>Role Hak Akses</th>
                    <th class="sipn-admin-data-col-action">Aksi Pengelolaan</th>
                </tr></thead>
                <tbody>
                <?php if ($users): ?>
                    <?php foreach ($users as $index => $row): ?>
                    <?php
                        $row_id = (int) $row['id'];
                        $row_role = (string) ($row['role'] ?? '');
                        $role_label = $role_labels[$row_role] ?? 'Tidak dikenal';
                        $role_pill = $role_pills[$row_role] ?? 'is-orange';
                        $role_icon = $role_icons[$row_role] ?? 'bi-person-fill';
                        $is_current_user = $row_id === $logged_in_user_id;
                        $avatar_role_class = in_array($row_role, ['admin', 'guru', 'siswa'], true) ? 'is-' . $row_role : '';
                    ?>
                    <tr data-admin-search-row style="--admin-row-delay: <?= min($index, 12) * 45; ?>ms;">
                        <td class="sipn-admin-data-number"><?= $index + 1; ?></td>
                        <td>
                            <div class="sipn-admin-data-person">
                                <span class="sipn-admin-data-avatar <?= $avatar_role_class; ?>"><i class="bi <?= $role_icon; ?>" aria-hidden="true"></i></span>
                                <span>
                                    <strong><?= $e($row['username']); ?></strong>
                                    <?php if ($is_current_user): ?><span class="sipn-admin-data-pill is-current">Akun Anda</span><?php endif; ?>
                                </span>
                            </div>
                        </td>
                        <td><span class="sipn-admin-data-pill <?= $role_pill; ?>"><?= $e($role_label); ?></span></td>
                        <td class="sipn-admin-data-actions" data-search-ignore>
                            <button type="button" class="sipn-admin-icon-button is-edit" data-modal-target="#modalEdit<?= $row_id; ?>" title="Edit akun" aria-label="Edit akun <?= $e($row['username']); ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                            <button type="button" class="sipn-admin-icon-button is-reset" data-modal-target="#modalReset<?= $row_id; ?>" title="Reset password" aria-label="Reset password <?= $e($row['username']); ?>"><i class="bi bi-key-fill" aria-hidden="true"></i></button>
                            <?php if (!$is_current_user): ?>
                                <button type="button" class="sipn-admin-icon-button is-delete" data-modal-target="#modalHapus<?= $row_id; ?>" title="Hapus akun" aria-label="Hapus akun <?= $e($row['username']); ?>"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="sipn-admin-data-empty"><i class="bi bi-person-x" aria-hidden="true"></i><strong>Belum ada akun pengguna</strong><span>Akun yang terdaftar akan muncul pada tabel ini.</span></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="sipn-admin-data-no-match" data-admin-search-empty hidden><i class="bi bi-search" aria-hidden="true"></i><strong>Data tidak ditemukan</strong><span>Coba kata kunci lain atau tekan tombol reset.</span></div>
        </div>
    </section>
</div>

<!-- Modal diletakkan di luar tabel agar struktur HTML tetap valid. -->
<?php foreach ($users as $row): ?>
<?php $row_id = (int) $row['id']; $row_username = $e($row['username']); $row_role = (string) ($row['role'] ?? ''); ?>
<div class="modal-overlay" id="modalEdit<?= $row_id; ?>">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalEditUserTitle<?= $row_id; ?>">
        <form method="POST">
            <div class="modal-header header-primary">
                <h5 id="modalEditUserTitle<?= $row_id; ?>">Edit Akun Pengguna</h5>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit_user">
                <input type="hidden" name="id_user" value="<?= $row_id; ?>">
                <div class="form-group">
                    <label class="form-label" for="usernameUser<?= $row_id; ?>">Username</label>
                    <input type="text" class="form-input" id="usernameUser<?= $row_id; ?>" name="username" value="<?= $row_username; ?>" maxlength="100" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="roleUser<?= $row_id; ?>">Role Hak Akses</label>
                    <select class="form-select" id="roleUser<?= $row_id; ?>" name="role" required>
                        <option value="admin" <?= $row_role === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                        <option value="guru" <?= $row_role === 'guru' ? 'selected' : ''; ?>>Guru</option>
                        <option value="siswa" <?= $row_role === 'siswa' ? 'selected' : ''; ?>>Siswa</option>
                    </select>
                </div>
                <?php if ($row_id === $logged_in_user_id): ?>
                    <p class="teacher-form-note"><i class="bi bi-info-circle" aria-hidden="true"></i> Perubahan role pada akun aktif akan mengubah hak akses sesi Anda.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom"><i class="bi bi-check2-circle" aria-hidden="true"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalReset<?= $row_id; ?>">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalResetUserTitle<?= $row_id; ?>">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5 id="modalResetUserTitle<?= $row_id; ?>">Reset Password</h5>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="reset_password">
                <input type="hidden" name="id_user" value="<?= $row_id; ?>">
                <p>Atur password baru untuk akun <strong><?= $row_username; ?></strong>.</p>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="passwordUser<?= $row_id; ?>">Password Baru</label>
                    <input type="password" class="form-input" id="passwordUser<?= $row_id; ?>" name="password_baru" minlength="4" autocomplete="new-password" placeholder="Masukkan password baru" required>
                    <small class="teacher-field-help">Minimal 4 karakter, sesuai ketentuan form sebelumnya.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-key-fill" aria-hidden="true"></i> Simpan Password</button>
            </div>
        </form>
    </div>
</div>

<?php if ($row_id !== $logged_in_user_id): ?>
<div class="modal-overlay" id="modalHapus<?= $row_id; ?>">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalHapusUserTitle<?= $row_id; ?>">
        <form method="POST">
            <div class="modal-header header-danger">
                <h5 id="modalHapusUserTitle<?= $row_id; ?>">Konfirmasi Hapus Akun</h5>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="hapus_user">
                <input type="hidden" name="id_user" value="<?= $row_id; ?>">
                <p>Hapus akun <strong><?= $row_username; ?></strong> secara permanen?</p>
                <p class="sipn-admin-user-warning"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> Penghapusan akun dapat berdampak pada data profil terkait, bergantung pada relasi database yang berlaku.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash3" aria-hidden="true"></i> Ya, Hapus Akun</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>

