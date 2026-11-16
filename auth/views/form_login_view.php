<?php
/**
 * View halaman form_login.
 * Variabel seperti $error disiapkan oleh controller.
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIPN - SMK Sangkuriang 1 Cimahi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css?v=sipn-modular-css-20261114">
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
n
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