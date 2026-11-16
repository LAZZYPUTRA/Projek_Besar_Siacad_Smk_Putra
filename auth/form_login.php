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

require __DIR__ . '/views/form_login_view.php';
