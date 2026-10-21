<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

$logged_in_user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'reset_password') {
        $id_user   = (int)$_POST['id_user'];
        $pass_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $pass_baru, $id_user);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Password pengguna berhasil diperbarui!');
        } else {
            set_flash_message('danger', 'Gagal memperbarui password.');
        }
    } 
    elseif ($aksi === 'edit_user') {
        $id_user  = (int)$_POST['id_user'];
        $username = sanitize($_POST['username']);
        $role     = sanitize($_POST['role']);

        $stmt_cek = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmt_cek, "si", $username, $id_user);
        mysqli_stmt_execute($stmt_cek);
        $res_cek = mysqli_stmt_get_result($stmt_cek);

        if (mysqli_num_rows($res_cek) > 0) {
            set_flash_message('danger', 'Gagal! Username tersebut sudah digunakan oleh akun lain.');
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $username, $role, $id_user);
            
            if (mysqli_stmt_execute($stmt)) {
                if ($id_user === $logged_in_user_id) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role']     = $role;
                }
                set_flash_message('success', 'Data akun user berhasil diperbarui!');
            } else {
                set_flash_message('danger', 'Gagal memperbarui data user.');
            }
        }
    }
    elseif ($aksi === 'hapus_user') {
        $id_user = (int)$_POST['id_user'];

        if ($id_user === $logged_in_user_id) {
            set_flash_message('danger', 'Gagal! Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_user);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Akun user berhasil dihapus!');
            } else {
                set_flash_message('danger', 'Gagal menghapus user.');
            }
        }
    }

    header("Location: data_user.php");
    exit();
}

require_once __DIR__ . '/../includes/template_header.php';

$query = "SELECT id, username, role FROM users ORDER BY role ASC, username ASC";
$result = mysqli_query($conn, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>

<div class="page-header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <i class="bi bi-people text-primary"></i> Kelola Akun User
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem; margin-bottom: 0;">Kelola hak akses role, kredensial login, dan reset kata sandi pengguna.</p>
    </div>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Username</th>
                    <th style="width: 25%;">Role Hak Akses</th>
                    <th style="width: 40%;" class="text-center">Aksi Pengelolaan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php $no = 1; foreach ($users as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['username']); ?></strong>
                            <?php if ($row['id'] === $logged_in_user_id): ?>
                                <span style="font-size: 0.7rem; color: #166534; background: #dcfce7; padding: 2px 6px; border-radius: 4px; margin-left: 0.4rem; font-weight: 700;">(Anda)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $row['role'] === 'admin' ? 'danger' : ($row['role'] === 'guru' ? 'primary' : 'success'); ?>">
                                <?= strtoupper($row['role']); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <!-- Wrapper Flexbox agar tombol sejajar rapi ke samping -->
                            <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; flex-wrap: nowrap;">
                               <button type="button" class="btn btn-sm btn-secondary" data-modal-target="#modalEdit<?= $row['id']; ?>" title="Edit User">
                                    <i class="bi bi-key"></i> Edit
                                <button type="button" class="btn btn-sm btn-warning" data-modal-target="#modalReset<?= $row['id']; ?>" title="Reset Password">
                                    <i class="bi bi-key"></i> Reset
                                </button>
                                <?php if ($row['id'] !== $logged_in_user_id): ?>
                                    <button type="button" class="btn btn-sm btn-danger" data-modal-target="#modalHapus<?= $row['id']; ?>" title="Hapus User">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center" style="text-align: center; padding: 2rem;">Belum ada data user.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL DI LUAR TABEL ================= -->
<?php foreach ($users as $row): ?>

<!-- 1. Modal Edit User & Role -->
<div class="modal-overlay" id="modalEdit<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-primary">
                <h5>Edit Akun User - <?= htmlspecialchars($row['username']); ?></h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit_user">
                <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($row['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role Akses</label>
                    <select class="form-select" name="role" required>
                        <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : ''; ?>>ADMIN</option>
                        <option value="guru" <?= $row['role'] === 'guru' ? 'selected' : ''; ?>>GURU</option>
                        <option value="siswa" <?= $row['role'] === 'siswa' ? 'selected' : ''; ?>>SISWA</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Reset Password -->
<div class="modal-overlay" id="modalReset<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5>Reset Password - <?= htmlspecialchars($row['username']); ?></h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="reset_password">
                <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" class="form-control" name="password_baru" required minlength="4" placeholder="Masukkan password baru...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-warning" style="background: #d97706; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Simpan Password</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Hapus User -->
<?php if ($row['id'] !== $logged_in_user_id): ?>
<div class="modal-overlay" id="modalHapus<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-danger">
                <h5>Konfirmasi Hapus Akun</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="hapus_user">
                <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                <p>Apakah Anda yakin ingin menghapus akun user <strong><?= htmlspecialchars($row['username']); ?></strong>?</p>
                <p style="font-size: 0.78rem; color: #dc2626; margin-top: 0.5rem; margin-bottom: 0;">
                    <i class="bi bi-exclamation-triangle-fill"></i> Peringatan: Menghapus akun ini juga dapat menghapus data profil yang terhubung.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger" style="background: #dc2626; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>