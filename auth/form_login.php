<?php
session_start();
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header("Location: " . BASE_URL . $role . "/dashboard_" . $role . ".php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Username dan Password wajib diisi!';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                set_flash_message('success', 'Selamat datang kembali, ' . htmlspecialchars($user['username']) . '!');

                header("Location: " . BASE_URL . $user['role'] . "/dashboard_" . $user['role'] . ".php");
                exit();
            } else {
                $error = 'Password yang Anda masukkan salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIPN - SMK Sangkuriang 1 Cimahi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css">
</head>
<body>

<div class="auth-enterprise-wrapper">
    <div class="auth-split-container">
        <!-- Sisi Kiri: Branding & Informasi -->
        <div class="auth-brand-side">
            <div class="brand-hero-top">
                <div class="hero-badge-pill">
                    <i class="bi bi-shield-shaded"></i> PORTAL AKADEMIK TERPADU
                </div>
            </div>
            <div class="brand-hero-content">
                <h1>Sistem Informasi Pengelolaan Nilai</h1>
                <p>Platform manajemen rekapitulasi capaian kompetensi peserta didik berbasis Kurikulum Merdeka SMK Sangkuriang 1 Cimahi.</p>
            </div>
            <div class="brand-hero-footer">
                &copy; 2026 SMK Sangkuriang 1 Cimahi. Secured Enterprise System.
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="auth-form-side">
            <div class="auth-form-container">
                <div class="form-side-header">
                    <h3>Masuk ke Akun</h3>
                    <p>Silakan masukkan kredensial resmi Anda untuk melanjutkan.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-4" role="alert" style="padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="auth-input-group">
                        <label for="username">Username / NIP / NIS</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-person-fill field-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Masukkan username..." required autofocus>
                        </div>
                    </div>

                    <div class="auth-input-group">
                        <label for="password">Kata Sandi</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-lock-fill field-icon"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••••••" required>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.4rem; color: var(--text-muted); cursor: pointer;">
                            <input type="checkbox" name="remember" style="accent-color: var(--primary-dark);"> Ingat saya
                        </label>
                        <a href="#" style="color: var(--primary-dark); font-weight: 700; text-decoration: none;">Lupa sandi?</a>
                    </div>

                    <button type="submit" class="auth-btn-submit">
                        Masuk ke Dashboard <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div style="margin-top: 2rem; text-align: center; font-size: 0.82rem; color: var(--text-muted);">
                    Belum memiliki akun siswa? <a href="form_register_siswa.php" style="color: var(--primary-dark); font-weight: 700; text-decoration: none;">Registrasi Siswa Baru</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>