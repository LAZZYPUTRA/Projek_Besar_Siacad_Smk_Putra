<?php
require_once __DIR__ . '/config/koneksi_database.php';

$username = 'admin';
$password = 'admin123'; // Password yang ingin digunakan
$role     = 'admin';

// Hash password menggunakan BCRYPT
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah username admin sudah ada
$stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt_check, "s", $username);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

if (mysqli_stmt_num_rows($stmt_check) > 0) {
    // Update password jika admin sudah ada
    $stmt_update = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = ?");
    mysqli_stmt_bind_param($stmt_update, "ss", $password_hashed, $username);
    if (mysqli_stmt_execute($stmt_update)) {
        echo "<h3 style='color:green;'>✓ Password Admin berhasil diperbarui!</h3>";
    }
} else {
    // Buat akun admin baru
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "sss", $username, $password_hashed, $role);
    if (mysqli_stmt_execute($stmt_insert)) {
        echo "<h3 style='color:green;'>✓ Akun Admin berhasil dibuat!</h3>";
    }
}

echo "Username: <b>admin</b><br>";
echo "Password: <b>admin123</b><br>";
echo "<a href='auth/form_login.php'>Klik di sini untuk Login</a>";
?>