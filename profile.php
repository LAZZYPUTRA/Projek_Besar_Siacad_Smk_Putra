<?php
require_once __DIR__ . '/includes/helper_cek_login.php';

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama']);
    
    // Update nama sesuai role
    if ($role === 'guru') {
        $stmt = mysqli_prepare($conn, "UPDATE guru SET nama=? WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "si", $nama, $user_id);
        mysqli_stmt_execute($stmt);
    } elseif ($role === 'siswa') {
        $stmt = mysqli_prepare($conn, "UPDATE siswa SET nama=? WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "si", $nama, $user_id);
        mysqli_stmt_execute($stmt);
    }

    // Update password jika diisi
    if (!empty($_POST['password_baru'])) {
        $pass_hash = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
        $stmt_p = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
        mysqli_stmt_bind_param($stmt_p, "si", $pass_hash, $user_id);
        mysqli_stmt_execute($stmt_p);
    }

    set_flash_message('success', 'Profil berhasil diperbarui!');
    header('Location: profile.php');
    exit();
}

require_once __DIR__ . '/includes/template_header.php';

// Ambil info detail
$username = $_SESSION['username'];
$nama_lengkap = $username;

if ($role === 'guru') {
    $q = mysqli_query($conn, "SELECT nama FROM guru WHERE user_id=$user_id");
    if ($d = mysqli_fetch_assoc($q)) $nama_lengkap = $d['nama'];
} elseif ($role === 'siswa') {
    $q = mysqli_query($conn, "SELECT nama FROM siswa WHERE user_id=$user_id");
    if ($d = mysqli_fetch_assoc($q)) $nama_lengkap = $d['nama'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Kelola Profil</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($username); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control text-uppercase" value="<?= htmlspecialchars($role); ?>" disabled>
                    </div>
                    <?php if ($role !== 'admin'): ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama_lengkap); ?>" required>
                    </div>
                    <?php endif; ?>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Password Baru (Kosongkan jika tidak diganti)</label>
                        <input type="password" name="password_baru" class="form-control" minlength="4">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/template_footer.php'; ?>