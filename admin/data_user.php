<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

$logged_in_user_id = (int) ($_SESSION['user_id'] ?? 0);
$allowed_roles = ['admin', 'guru', 'siswa'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = (string) $_POST['aksi'];
    $id_user = (int) ($_POST['id_user'] ?? 0);

    if ($aksi === 'reset_password') {
        $password_baru = (string) ($_POST['password_baru'] ?? '');

        if ($id_user <= 0 || strlen($password_baru) < 4) {
            set_flash_message('danger', 'Reset password gagal. Pastikan akun valid dan password minimal 4 karakter.');
        } else {
            $pass_baru = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'si', $pass_baru, $id_user);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    set_flash_message('success', 'Password pengguna berhasil diperbarui!');
                } else {
                    set_flash_message('danger', 'Gagal memperbarui password.');
                }
                mysqli_stmt_close($stmt);
            } else {
                set_flash_message('danger', 'Permintaan reset password tidak dapat diproses.');
            }
        }
    } elseif ($aksi === 'edit_user') {
        $username = trim((string) ($_POST['username'] ?? ''));
        $role = (string) ($_POST['role'] ?? '');

        if ($id_user <= 0 || $username === '' || !in_array($role, $allowed_roles, true)) {
            set_flash_message('danger', 'Data akun tidak valid. Periksa username dan role yang dipilih.');
        } else {
            $stmt_cek = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1');

            if (!$stmt_cek) {
                set_flash_message('danger', 'Pemeriksaan username gagal dijalankan.');
            } else {
                mysqli_stmt_bind_param($stmt_cek, 'si', $username, $id_user);
                mysqli_stmt_execute($stmt_cek);
                $res_cek = mysqli_stmt_get_result($stmt_cek);
                $username_dipakai = $res_cek && mysqli_num_rows($res_cek) > 0;
                mysqli_stmt_close($stmt_cek);

                if ($username_dipakai) {
                    set_flash_message('danger', 'Gagal! Username tersebut sudah digunakan oleh akun lain.');
                } else {
                    $stmt = mysqli_prepare($conn, 'UPDATE users SET username = ?, role = ? WHERE id = ?');
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'ssi', $username, $role, $id_user);
                        if (mysqli_stmt_execute($stmt)) {
                            if ($id_user === $logged_in_user_id) {
                                $_SESSION['username'] = $username;
                                $_SESSION['role'] = $role;
                            }
                            set_flash_message('success', 'Data akun user berhasil diperbarui!');
                        } else {
                            set_flash_message('danger', 'Gagal memperbarui data user.');
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        set_flash_message('danger', 'Data akun tidak dapat diperbarui saat ini.');
                    }
                }
            }
        }
    } elseif ($aksi === 'hapus_user') {
        if ($id_user <= 0) {
            set_flash_message('danger', 'Akun yang dipilih tidak valid.');
        } elseif ($id_user === $logged_in_user_id) {
            set_flash_message('danger', 'Gagal! Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id_user);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    set_flash_message('success', 'Akun user berhasil dihapus!');
                } else {
                    set_flash_message('danger', 'Gagal menghapus user. Akun mungkin sudah tidak tersedia atau masih terhubung dengan data lain.');
                }
                mysqli_stmt_close($stmt);
            } else {
                set_flash_message('danger', 'Permintaan penghapusan akun tidak dapat diproses.');
            }
        }
    }

    header('Location: data_user.php');
    exit();
}

$query = 'SELECT id, username, role FROM users ORDER BY role ASC, username ASC';
$result = mysqli_query($conn, $query);
$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

$total_user = count($users);
$total_admin = count(array_filter($users, static fn($row) => ($row['role'] ?? '') === 'admin'));
$total_guru = count(array_filter($users, static fn($row) => ($row['role'] ?? '') === 'guru'));
$total_siswa = count(array_filter($users, static fn($row) => ($row['role'] ?? '') === 'siswa'));
$data_user_error = !$result;

$e = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$role_labels = [
    'admin' => 'Administrator',
    'guru' => 'Guru',
    'siswa' => 'Siswa',
];
$role_pills = [
    'admin' => 'is-red',
    'guru' => 'is-blue',
    'siswa' => 'is-green',
];
$role_icons = [
    'admin' => 'bi-shield-lock-fill',
    'guru' => 'bi-person-workspace',
    'siswa' => 'bi-mortarboard-fill',
];

require_once __DIR__ . '/../includes/template_header.php';
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

<script src="<?= BASE_URL; ?>assets/js/admin-table-search.js?v=admin-user-20261108" defer></script>
<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>