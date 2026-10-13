<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'reset_password') {
        $id_user  = (int)$_POST['id_user'];
        $pass_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $pass_baru, $id_user);
        mysqli_stmt_execute($stmt) 
            ? set_flash_message('success', 'Password pengguna berhasil diperbarui!') 
            : set_flash_message('danger', 'Gagal mengedit password.');
    }
    header("Location: data_user.php");
    exit();
}


require_once __DIR__ . '/../includes/template_header.php';

$query = "SELECT id, username, role FROM users ORDER BY role ASC, username ASC";
$result = mysqli_query($conn, $query);
?>

<div class="page-header">
    <h2><i class="bi bi-people"></i> Kelola Akun User</h2>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th style="width: 20%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><strong><?= htmlspecialchars($row['username']); ?></strong></td>
                    <td>
                        <span class="badge badge-<?= $row['role'] === 'admin' ? 'danger' : ($row['role'] === 'guru' ? 'primary' : 'success'); ?>">
                            <?= strtoupper($row['role']); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" data-modal-target="#modalReset<?= $row['id']; ?>">
                            <i class="bi bi-key"></i> Reset Password
                        </button>
                    </td>
                </tr>

                <!-- Modal Reset Password (Native Overlay) -->
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
                                    <input type="password" class="form-input" name="password_baru" required minlength="4">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                                <button type="submit" class="btn btn-warning">Simpan Password</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>