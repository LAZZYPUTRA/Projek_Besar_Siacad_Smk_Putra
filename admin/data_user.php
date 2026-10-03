<?php
require_once __DIR__ . '/../config/koneksi_database.php';
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

<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
    <h2><i class="bi bi-people text-danger me-2"></i>Kelola Akun User</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th width="20%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><strong><?= htmlspecialchars($row['username']); ?></strong></td>
                    <td>
                        <span class="badge bg-<?= $row['role'] === 'admin' ? 'danger' : ($row['role'] === 'guru' ? 'primary' : 'success'); ?>">
                            <?= strtoupper($row['role']); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalReset<?= $row['id']; ?>">
                            <i class="bi bi-key"></i> Reset Password
                        </button>
                    </td>
                </tr>

                <!-- Modal Reset Password -->
                <div class="modal fade" id="modalReset<?= $row['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Reset Password - <?= htmlspecialchars($row['username']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="aksi" value="reset_password">
                                    <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" name="password_baru" required minlength="4">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning">Simpan Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>