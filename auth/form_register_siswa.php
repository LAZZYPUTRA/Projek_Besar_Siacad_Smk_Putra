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
        header('Location: ' . BASE_URL . 'auth/form_login.php');
        exit();
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        set_flash_message('danger', 'Gagal registrasi! NIS atau Username mungkin sudah terdaftar.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Siswa - SIPN SMK Sangkuriang 1</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/style.css?v=flash-dashboard-1">
</head>
<body class="auth-page">

<div class="auth-enterprise-wrapper" style="justify-content: center; align-items: center; padding: 2rem 1rem;">
    <div style="width: 100%; max-width: 580px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.06); overflow: hidden;">
        
        <div style="background: var(--primary-dark); color: #ffffff; padding: 2rem; text-align: center;">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.3rem;">Registrasi Akun Siswa</h2>
            <p style="font-size: 0.8rem; opacity: 0.8;">Sistem Informasi Pengelolaan Nilai • SMK Sangkuriang 1 Cimahi</p>
        </div>

        <div style="padding: 2rem;">
            <?php display_flash_message(); ?>

            <form method="POST">
                <div class="auth-input-group">
                    <label>Nomor Induk Siswa (NIS)</label>
                    <div class="auth-input-wrapper">
                        <i class="bi bi-card-text field-icon"></i>
                        <input type="text" name="nis" maxlength="10" placeholder="Contoh: 2425100123" required>
                    </div>
                </div>

                <div class="auth-input-group">
                    <label>Nama Lengkap Siswa</label>
                    <div class="auth-input-wrapper">
                        <i class="bi bi-person field-icon"></i>
                        <input type="text" name="nama" placeholder="Sesuai ijazah atau dapodik..." required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="auth-input-group">
                        <label>Kelas / Rombel</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-people field-icon"></i>
                            <input type="text" name="kelas" placeholder="Cth: XII RPL 1" required>
                        </div>
                    </div>

                    <div class="auth-input-group">
                        <label>Jenis Kelamin</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-gender-ambiguous field-icon"></i>
                            <select name="jenis_kelamin" style="width: 100%; padding: 0.75rem 0.8rem 0.75rem 2.6rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; font-size: 0.9rem; color: var(--text-dark); outline: none;" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px dashed var(--border-color); margin: 1.5rem 0; padding-top: 1rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Kredensial Akun Login</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="auth-input-group">
                        <label>Username</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-at field-icon"></i>
                            <input type="text" name="username" placeholder="Buat username..." required>
                        </div>
                    </div>

                    <div class="auth-input-group">
                        <label>Kata Sandi</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-btn-submit">
                    Daftar Sekarang <i class="bi bi-check-circle"></i>
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.82rem;">
                <a href="form_login.php" style="color: var(--primary-dark); font-weight: 700; text-decoration: none;"><i class="bi bi-arrow-left"></i> Sudah punya akun? Masuk di sini</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>