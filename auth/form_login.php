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
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan Password wajib diisi!';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                // Regenerasi ID session setelah autentikasi berhasil.
                session_regenerate_id(true);

                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['sipn_dashboard_entry'] = true;

                set_flash_message('success', 'Selamat datang kembali, ' . $user['username'] . '!');

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
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css?v=admin-logout-20261108">
</head>
<body class="auth-page">

<div class="auth-enterprise-wrapper auth-login-page">
    <main class="auth-split-container">
        <!-- Panel identitas SIPN -->
        <section class="auth-brand-side" aria-label="Informasi SIPN">
            <div class="auth-brand-inner">
                <div class="brand-hero-top">
                    <div class="auth-brand-symbol" aria-hidden="true">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="hero-badge-pill">
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                        PORTAL AKADEMIK TERPADU
                    </div>
                </div>

                <div class="brand-hero-content">
                    <span class="auth-brand-eyebrow">SMK SANGKURIANG 1 CIMAHI</span>
                    <h1>Sistem Informasi<br>Pengelolaan Nilai</h1>
                    <p>Platform pengelolaan nilai dan capaian kompetensi peserta didik dalam satu sistem akademik yang terintegrasi.</p>
                    <div class="auth-brand-divider" aria-hidden="true"></div>
                    <div class="auth-brand-caption">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        Akses resmi untuk admin, guru, dan siswa
                    </div>
                </div>

                <footer class="brand-hero-footer">
                    <span>&copy; 2026 SMK Sangkuriang 1 Cimahi</span>
                    <span class="auth-secure-label"><i class="bi bi-lock-fill" aria-hidden="true"></i> SISTEM TERLINDUNGI</span>
                </footer>
            </div>
        </section>

        <!-- Panel form login -->
        <section class="auth-form-side" aria-label="Form masuk SIPN">
            <div class="auth-form-container">
                <div class="auth-mobile-brand" aria-hidden="true">
                    <span class="auth-mobile-brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
                    <span><strong>SIPN</strong><small>SMK Sangkuriang 1 Cimahi</small></span>
                </div>

                <div class="auth-login-card">
                    <div class="form-side-header">
                        <span class="auth-form-eyebrow">AKSES PENGGUNA</span>
                        <h2>Selamat Datang</h2>
                        <p>Masuk menggunakan akun resmi sekolah Anda untuk melanjutkan.</p>
                    </div>

                    <?php display_flash_message(); ?>

                    <?php if (!empty($error)): ?>
                        <div class="auth-login-error" id="login-error" role="alert">
                            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                            <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="auth-login-form">
                        <div class="auth-input-group">
                            <label for="username">Username / NIP / NIS</label>
                            <div class="auth-input-wrapper">
                                <i class="bi bi-person field-icon" aria-hidden="true"></i>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    placeholder="Masukkan username Anda"
                                    autocomplete="username"
                                    value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php if (!empty($error)): ?>aria-describedby="login-error"<?php endif; ?>
                                    required
                                >
                            </div>
                        </div>

                        <div class="auth-input-group">
                            <label for="password">Kata Sandi</label>
                            <div class="auth-input-wrapper">
                                <i class="bi bi-lock field-icon" aria-hidden="true"></i>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan kata sandi"
                                    autocomplete="current-password"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" class="auth-btn-submit">
                            <span>Masuk ke Dashboard</span>
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>

                    <div class="auth-register-note">
                        <span>Belum memiliki akun siswa?</span>
                        <a href="form_register_siswa.php">Registrasi Siswa Baru <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                    </div>
                </div>

                <p class="auth-form-footnote">
                    <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    Gunakan akun yang telah diberikan oleh pihak sekolah.
                </p>
            </div>
        </section>
    </main>
</div>
</body>
</html>