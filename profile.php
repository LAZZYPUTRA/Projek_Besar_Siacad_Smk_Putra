<?php
// Impor file konfigurasi dan helper dari direktori root
require_once __DIR__ . '/config/koneksi_database.php';
require_once __DIR__ . '/includes/helper_fungsi.php';
require_once __DIR__ . '/includes/helper_cek_login.php';

// Izinkan semua role yang sudah login untuk mengakses profil mereka
check_role(['admin', 'guru', 'siswa']);

$user_id = $_SESSION['user_id'];

// Ambil data user yang sedang login
$stmt = mysqli_prepare($conn, "SELECT id, username, role FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = sanitize($_POST['aksi']);

    if ($aksi === 'update_profile') {
        $username_baru = sanitize($_POST['username']);

        // Cek duplikasi username
        $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($cek, "si", $username_baru, $user_id);
        mysqli_stmt_execute($cek);
        $res_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($res_cek) > 0) {
            set_flash_message('danger', 'Gagal! Username tersebut sudah digunakan oleh akun lain.');
        } else {
            $update = mysqli_prepare($conn, "UPDATE users SET username = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "si", $username_baru, $user_id);
            if (mysqli_stmt_execute($update)) {
                $_SESSION['username'] = $username_baru;
                set_flash_message('success', 'Profil berhasil diperbarui!');
            } else {
                set_flash_message('danger', 'Gagal memperbarui profil.');
            }
        }
    } 
    elseif ($aksi === 'update_password') {
        $password_lama = $_POST['password_lama'];
        $password_baru = $_POST['password_baru'];
        $konfirmasi    = $_POST['konfirmasi_password'];

        if ($password_baru !== $konfirmasi) {
            set_flash_message('danger', 'Gagal! Konfirmasi password baru tidak cocok.');
        } else {
            // Ambil password lama dari DB
            $q_pass = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
            mysqli_stmt_bind_param($q_pass, "i", $user_id);
            mysqli_stmt_execute($q_pass);
            $res_pass = mysqli_stmt_get_result($q_pass);
            $data_user = mysqli_fetch_assoc($res_pass);

            if (password_verify($password_lama, $data_user['password'])) {
                $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
                $up_pass = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
                mysqli_stmt_bind_param($up_pass, "si", $hash_baru, $user_id);
                
                if (mysqli_stmt_execute($up_pass)) {
                    set_flash_message('success', 'Kata sandi berhasil diubah!');
                } else {
                    set_flash_message('danger', 'Gagal mengubah kata sandi.');
                }
            } else {
                set_flash_message('danger', 'Gagal! Kata sandi lama yang Anda masukkan salah.');
            }
        }
    }

    header("Location: profil.php");
    exit();
}

require_once __DIR__ . '/includes/template_header.php';
?>

<!-- Header Halaman -->
<div class="page-header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <i class="bi bi-person-circle text-primary"></i> Pengaturan Profil Akun
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem; margin-bottom: 0;">Kelola kredensial username dan pembaruan kata sandi akun Anda.</p>
    </div>
</div>

<!-- Layout Grid Form Profil -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; width: 100%;">

    <!-- Card 1: Informasi Akun & Update Username -->
    <div class="card-panel">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary-dark); color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800;">
                <?= strtoupper(substr($user['username'], 0, 1)); ?>
            </div>
            <div>
                <h5 style="font-size: 1rem; font-weight: 800; color: var(--text-dark); margin: 0;"><?= htmlspecialchars($user['username']); ?></h5>
                <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'guru' ? 'primary' : 'success'); ?>" style="margin-top: 0.3rem; display: inline-block;">
                    <?= strtoupper($user['role']); ?>
                </span>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="aksi" value="update_profile">
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Username Login</label>
                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Card 2: Keamanan (Ganti Password) -->
    <div class="card-panel">
        <div style="margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h5 style="font-size: 1rem; font-weight: 800; color: var(--text-dark); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-shield-lock-fill text-warning"></i> Keamanan & Kata Sandi
            </h5>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem; margin-bottom: 0;">Disarankan menggunakan kombinasi kata sandi yang kuat.</p>
        </div>

        <form method="POST">
            <input type="hidden" name="aksi" value="update_password">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Kata Sandi Lama</label>
                <input type="password" class="form-control" name="password_lama" placeholder="••••••••" required>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Kata Sandi Baru</label>
                <input type="password" class="form-control" name="password_baru" placeholder="••••••••" required minlength="4">
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                <input type="password" class="form-control" name="konfirmasi_password" placeholder="••••••••" required minlength="4">
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-warning" style="background-color: #d97706; color: #fff;">
                    <i class="bi bi-key"></i> Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

</div>

<?php require_once __DIR__ . '/includes/template_footer.php';   ?>