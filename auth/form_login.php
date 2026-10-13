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

    <!-- Login Card Panel -->
    <div class="auth-card-wrapper">
        <div class="login-card">
            <div class="accent-top-line"></div>
            <div class="login-card-body">
                
                <!-- Brand Header Inside Card -->
                <div class="brand-identity-center">
                    <div class="app-logo-badge">
                        <i class="bi bi-journal-check"></i>
                        <span>SIPN</span>
                    </div>
                    <div class="version-badge-pill">V3.2</div>
                    <div class="school-tag-pill">• SMK SANGKURIANG 1 CIMAHI</div>
                    
                    <h2 class="auth-heading">Masuk ke Portal SIPN</h2>
                    <p class="auth-subheading">Sistem Informasi Pengelolaan Nilai Terpadu Berbasis Standar Kompetensi Nasional</p>
                </div>

                <!-- Announcement Banner -->
                <div class="notice-banner">
                    <i class="bi bi-megaphone-fill notice-icon"></i>
                    <div class="notice-content">
                        <div class="notice-title">WARTA AKADEMIK AKTIF • T.A 2024/2025</div>
                        <div class="notice-desc">Periode Penginputan Nilai: Semester Genap T.A 2024/2025 Sedang Berlangsung. Mohon selesaikan rekapitulasi sebelum batas tenggat.</div>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Role Selector -->
                <div class="role-selector-group">
                    <label class="role-label">Pilih Akses Pengguna</label>
                    <div class="role-pills">
                        <button type="button" class="role-btn" data-role="admin"><i class="bi bi-shield-lock"></i> Admin</button>
                        <button type="button" class="role-btn" data-role="guru"><i class="bi bi-person-badge"></i> Guru</button>
                        <button type="button" class="role-btn active" data-role="siswa"><i class="bi bi-mortarboard"></i> Siswa</button>
                    </div>
                </div>

                <!-- Form Login -->
                <form action="" method="POST" class="auth-form">
                    <div class="form-group">
                        <div class="form-label-row">
                            <label for="username" class="form-label">Username / NIP / NISN</label>
                            <span class="label-req">Wajib diisi</span>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="bi bi-person-vcard"></i></span>
                            <input type="text" class="form-input" id="username" name="username" placeholder="Masukkan NIP, NISN, atau username..." required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label-row">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <span class="label-hint">Min. 8 karakter</span>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-input" id="password" name="password" placeholder="••••••••••••" required>
                            <button type="button" class="btn-toggle-pwd" id="togglePwd"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div class="form-options-row">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Ingat saya di perangkat ini
                        </label>
                        <a href="#" class="forgot-link">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn-submit-primary">
                        Masuk ke Sistem <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <!-- Promotion Box Registration -->
                <div class="register-promo-box">
                    <div class="promo-left">
                        <div class="promo-icon"><i class="bi bi-person-plus-fill"></i></div>
                        <div class="promo-text">
                            <strong>Belum memiliki akun siswa?</strong>
                            <span>Aktivasi identitas siswa menggunakan NISN aktif</span>
                        </div>
                    </div>
                    <a href="form_register_siswa.php" class="btn-promo">Registrasi Baru</a>
                </div>

                <!-- Security Badges Footer inside Card -->
                <div class="card-footer-notes">
                    <span><i class="bi bi-check-square-fill"></i> Terintegrasi Dapodik Kemendikbudristek</span>
                    <span class="dot">•</span>
                    <span><i class="bi bi-shield-check"></i> Koneksi TLS 256-bit Terenkripsi</span>
                </div>

            </div>
        </div>

        <div class="help-center-link">
            <i class="bi bi-question-circle"></i> Kendala akses? Hubungi <a href="#">Pusat Bantuan IT SMK Sangkuriang 1</a>
        </div>
    </div>

    <!-- Footer Page -->
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const togglePwd = document.getElementById('togglePwd');
    const pwdInput = document.getElementById('password');
    if (togglePwd && pwdInput) {
        togglePwd.addEventListener('click', () => {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            togglePwd.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    const roleBtns = document.querySelectorAll('.role-btn');
    roleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            roleBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>