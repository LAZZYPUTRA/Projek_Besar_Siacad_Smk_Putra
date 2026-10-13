<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nis      = sanitize($_POST['nis']);
    $nama     = sanitize($_POST['nama']);
    $kelas    = sanitize($_POST['kelas']);
    $jk       = sanitize($_POST['jenis_kelamin']);

    mysqli_begin_transaction($conn);
    try {
        $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'siswa')");
        mysqli_stmt_bind_param($stmt1, "ss", $username, $password);
        mysqli_stmt_execute($stmt1);
        $user_id = mysqli_insert_id($conn);

        $stmt2 = mysqli_prepare($conn, "INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, "ssssi", $nis, $nama, $kelas, $jk, $user_id);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($conn);
        set_flash_message('success', 'Registrasi berhasil! Silakan login.');
        header('Location: form_login.php');
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        set_flash_message('danger', 'Gagal registrasi! NIS atau Username mungkin sudah terdaftar.');
    }
}

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="auth-page-wrapper">
    <!-- Top Bar Navigation Header -->
    <div class="auth-top-bar">
        <div class="brand-box">
            <div class="brand-icon-box">
                <i class="bi bi-journal-check"></i>
            </div>
            <div class="brand-text">
                <h1>SIPN</h1>
                <span>SMK SANGKURIANG 1 CIMAHI</span>
            </div>
        </div>
        <div class="academic-badge">
            <i class="bi bi-gear-wide-connected"></i> ACADEMIC PORTAL
        </div>
    </div>

    <!-- Split Register Container -->
    <div class="register-card-container">
        <!-- Sidebar Kiri (Hijau Tua) -->
        <div class="register-sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon-sm"><i class="bi bi-journal-check"></i></div>
                <div>
                    <h3>SIPN</h3>
                    <span>SMK SANGKURIANG 1 CIMAHI</span>
                </div>
            </div>

            <div class="sidebar-tag">
                <i class="bi bi-mortarboard"></i> T.A 2024/2025 • Kurikulum Merdeka
            </div>

            <h2 class="sidebar-title">Portal Rapor & Buku Induk Siswa</h2>
            <p class="sidebar-desc">Daftarkan akun peserta didik Anda untuk verifikasi identitas resmi dan pembukaan akses capaian kompetensi digital secara terpadu.</p>

            <div class="stepper-vertical">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <div class="step-info">
                        <strong>Identitas Siswa</strong>
                        <span>Validasi NIS & Data Dapodik</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-info">
                        <strong>Kredensial Login</strong>
                        <span>Username & Kata Sandi Akun</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-footer-note">
                <i class="bi bi-arrow-repeat"></i> Sinkronisasi otomatis basis data Dapodik Kemendikbudristek.
            </div>
        </div>

        <!-- Form Kanan (Putih) -->
        <div class="register-form-panel">
            <div class="panel-header-row">
                <h2 class="panel-title">Registrasi Siswa Baru</h2>
                <span class="auth-badge">AUTH/REGISTER</span>
            </div>
            <p class="panel-subtitle">Lengkapi data di bawah ini dengan cermat sesuai catatan ijazah resmi Anda.</p>

            <!-- Step Indicator Pills -->
            <div class="step-pills-row">
                <span class="pill-step active"><span class="dot-active"></span> Tahap 1: Data Identitas</span>
                <span class="pill-step"><span class="dot-inactive"></span> Tahap 2: Kredensial</span>
            </div>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <div class="form-label-row">
                        <label class="form-label">Nomor Induk Siswa (NIS)</label>
                        <span class="label-hint">Maks. 10 Angka</span>
                    </div>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="bi bi-card-text"></i></span>
                        <input type="text" name="nis" class="form-input" maxlength="10" required placeholder="Contoh: 2122100452">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap Siswa</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="bi bi-person"></i></span>
                        <input type="text" name="nama" class="form-input" required placeholder="Sesuai ijazah SMP atau Dapodik">
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label class="form-label">Rombongan Belajar (Kelas)</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="bi bi-people"></i></span>
                            <input type="text" name="kelas" class="form-input" required placeholder="Contoh: XII RPL 1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="gender-radio-group">
                            <label class="radio-card">
                                <input type="radio" name="jenis_kelamin" value="L" checked>
                                <span class="radio-custom"></span> Laki-laki
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="jenis_kelamin" value="P">
                                <span class="radio-custom"></span> Perempuan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-section-divider">
                    <span>Kredensial Akun Login</span>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="bi bi-at"></i></span>
                            <input type="text" name="username" class="form-input" required placeholder="Buat username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-input" required placeholder="Buat password">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit-primary">
                    Daftar Sekarang <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="register-footer-links">
                <a href="form_login.php" class="back-login-link"><i class="bi bi-arrow-left"></i> Sudah memiliki akun? Masuk di sini</a>
                <span class="dapodik-verify"><i class="bi bi-shield-check"></i> Verifikasi Dapodik Otomatis</span>
            </div>
        </div>
    </div>

    <!-- Outer Page Footer -->
    <div class="auth-outer-footer">
        <div class="footer-left">
            <span><i class="bi bi-patch-check-fill"></i> Akreditasi A Unggul (BAN-SM)</span>
            <span class="dot">•</span>
            <span><i class="bi bi-lock-fill"></i> SSL 256-Bit Encrypted</span>
        </div>
        <div class="footer-right">
            &copy; 2024 SIPN SMK Sangkuriang 1 Cimahi. Hak Cipta Dilindungi.
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>