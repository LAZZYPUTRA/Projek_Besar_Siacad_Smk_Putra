<?php
require_once __DIR__ . '/config/koneksi_database.php';
require_once __DIR__ . '/includes/helper_fungsi.php';
require_once __DIR__ . '/includes/helper_cek_login.php';

check_login();

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

$query_user = mysqli_query($conn, "SELECT username FROM users WHERE id = '$user_id'");
$data_user  = mysqli_fetch_assoc($query_user);

$profil = [];
if ($role === 'guru') {
    $q_profil = mysqli_query($conn, "SELECT * FROM guru WHERE user_id = '$user_id'");
    $profil   = mysqli_fetch_assoc($q_profil);
} elseif ($role === 'siswa') {
    $q_profil = mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = '$user_id'");
    $profil   = mysqli_fetch_assoc($q_profil);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $username      = sanitize($_POST['username']);
    $password_baru = $_POST['password'];

    if (!empty($password_baru)) {
        $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
        $stmt_user       = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
        $stmt_user->bind_param("ssi", $username, $hashed_password, $user_id);
    } else {
        $stmt_user = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt_user->bind_param("si", $username, $user_id);
    }
    $stmt_user->execute();
    $stmt_user->close();

    if ($role === 'guru') {
        $nama = sanitize($_POST['nama']);
        $nip  = sanitize($_POST['nip']);
        $jk   = sanitize($_POST['jenis_kelamin']);

        $stmt_guru = $conn->prepare("UPDATE guru SET nama = ?, nip = ?, jenis_kelamin = ? WHERE user_id = ?");
        $stmt_guru->bind_param("sssi", $nama, $nip, $jk, $user_id);
        $stmt_guru->execute();
        $stmt_guru->close();
    } elseif ($role === 'siswa') {
        $nama  = sanitize($_POST['nama']);
        $nis   = sanitize($_POST['nis']);
        $kelas = sanitize($_POST['kelas']);
        $jk    = sanitize($_POST['jenis_kelamin']);

        $stmt_siswa = $conn->prepare("UPDATE siswa SET nama = ?, nis = ?, kelas = ?, jenis_kelamin = ? WHERE user_id = ?");
        $stmt_siswa->bind_param("ssssi", $nama, $nis, $kelas, $jk, $user_id);
        $stmt_siswa->execute();
        $stmt_siswa->close();
    }

    $_SESSION['username'] = $username;
    set_flash_message('success', 'Pengaturan akun dan profil berhasil diperbarui!');
    header("Location: profile.php");
    exit;
}

require_once __DIR__ . '/includes/template_header.php';
?>

<div class="centered-layout">
    <div class="card-panel profile-card">
        <div class="card-header header-primary">
            <h5><i class="bi bi-person-gear"></i> Pengaturan Akun & Profil</h5>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="form-section-title">Informasi Akun (Login)</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input" value="<?= htmlspecialchars($data_user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-input" placeholder="Kosongkan jika tidak diubah">
                        <small class="help-text">Isi hanya jika ingin mengganti password.</small>
                    </div>
                </div>

                <?php if ($role === 'guru' || $role === 'siswa'): ?>
                    <div class="form-section-title">Data Identitas Profil</div>
                    
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-input" value="<?= htmlspecialchars($profil['nama'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <?php if ($role === 'guru'): ?>
                                <label class="form-label">NIP</label>
                                <input type="text" name="nip" class="form-input" value="<?= htmlspecialchars($profil['nip'] ?? ''); ?>" required>
                            <?php elseif ($role === 'siswa'): ?>
                                <label class="form-label">NIS</label>
                                <input type="text" name="nis" class="form-input" value="<?= htmlspecialchars($profil['nis'] ?? ''); ?>" required>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" <?= (isset($profil['jenis_kelamin']) && $profil['jenis_kelamin'] === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?= (isset($profil['jenis_kelamin']) && $profil['jenis_kelamin'] === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($role === 'siswa'): ?>
                        <div class="form-group">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-input" value="<?= htmlspecialchars($profil['kelas'] ?? ''); ?>" required placeholder="Contoh: X RPL 1">
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Anda login sebagai <strong>Admin</strong>. Hanya pengaturan kredensial yang dapat diubah.
                    </div>
                <?php endif; ?>

                <div class="form-actions align-right">
                    <button type="submit" name="update_profil" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/template_footer.php'; ?>