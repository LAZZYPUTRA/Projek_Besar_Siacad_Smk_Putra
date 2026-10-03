<?php
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';

$id_user = $_SESSION['id_user']; // Sesuaikan dengan session ID login Anda

// Proses Update Password
if (isset($_POST['update_password'])) {
    $password_baru = $_POST['password_baru'];
    $konfirmasi    = $_POST['konfirmasi_password'];

    if ($password_baru === $konfirmasi) {
        // Menggunakan prepared statement untuk keamanan (Phase 3)
        $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $id_user);
        
        if ($stmt->execute()) {
            set_flash_message('success', 'Password berhasil diperbarui!');
        } else {
            set_flash_message('error', 'Gagal memperbarui password.');
        }
        $stmt->close();
    } else {
        set_flash_message('error', 'Password baru dan konfirmasi tidak cocok!');
    }
    header("Location: " . BASE_URL . "admin/profile_admin.php");
    exit;
}

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Profil Admin</h5>
            </div>
            <div class="card-body">
                <p><strong>Nama:</strong> <?= htmlspecialchars($_SESSION['nama_user'] ?? 'Admin'); ?></p>
                <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username'] ?? '-'); ?></p>
                <p><strong>Role:</strong> Administrator</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" name="update_password" class="btn btn-warning w-100">Simpan Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>