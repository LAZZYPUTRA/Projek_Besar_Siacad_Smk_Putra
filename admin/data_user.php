<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

$logged_in_user_id = (int) ($_SESSION['user_id'] ?? 0);
$allowed_roles = ['admin', 'guru', 'siswa'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = (string) $_POST['aksi'];
    $id_user = (int) ($_POST['id_user'] ?? 0);

    if ($aksi === 'reset_password') {
        $password_baru = (string) ($_POST['password_baru'] ?? '');

        if ($id_user <= 0 || strlen($password_baru) < 4) {
            set_flash_message('danger', 'Reset password gagal. Pastikan akun valid dan password minimal 4 karakter.');
        } else {
            $pass_baru = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'si', $pass_baru, $id_user);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    set_flash_message('success', 'Password pengguna berhasil diperbarui!');
                } else {
                    set_flash_message('danger', 'Gagal memperbarui password.');
                }
                mysqli_stmt_close($stmt);
            } else {
                set_flash_message('danger', 'Permintaan reset password tidak dapat diproses.');
            }
        }
    } elseif ($aksi === 'edit_user') {
        $username = trim((string) ($_POST['username'] ?? ''));
        $role = (string) ($_POST['role'] ?? '');

        if ($id_user <= 0 || $username === '' || !in_array($role, $allowed_roles, true)) {
            set_flash_message('danger', 'Data akun tidak valid. Periksa username dan role yang dipilih.');
        } else {
            $stmt_cek = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1');

            if (!$stmt_cek) {
                set_flash_message('danger', 'Pemeriksaan username gagal dijalankan.');
            } else {
                mysqli_stmt_bind_param($stmt_cek, 'si', $username, $id_user);
                mysqli_stmt_execute($stmt_cek);
                $res_cek = mysqli_stmt_get_result($stmt_cek);
                $username_dipakai = $res_cek && mysqli_num_rows($res_cek) > 0;
                mysqli_stmt_close($stmt_cek);

                if ($username_dipakai) {
                    set_flash_message('danger', 'Gagal! Username tersebut sudah digunakan oleh akun lain.');
                } else {
                    $stmt = mysqli_prepare($conn, 'UPDATE users SET username = ?, role = ? WHERE id = ?');
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'ssi', $username, $role, $id_user);
                        if (mysqli_stmt_execute($stmt)) {
                            if ($id_user === $logged_in_user_id) {
                                $_SESSION['username'] = $username;
                                $_SESSION['role'] = $role;
                            }
                            set_flash_message('success', 'Data akun user berhasil diperbarui!');
                        } else {
                            set_flash_message('danger', 'Gagal memperbarui data user.');
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        set_flash_message('danger', 'Data akun tidak dapat diperbarui saat ini.');
                    }
                }
            }
        }
    } elseif ($aksi === 'hapus_user') {
        if ($id_user <= 0) {
            set_flash_message('danger', 'Akun yang dipilih tidak valid.');
        } elseif ($id_user === $logged_in_user_id) {
            set_flash_message('danger', 'Gagal! Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id_user);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    set_flash_message('success', 'Akun user berhasil dihapus!');
                } else {
                    set_flash_message('danger', 'Gagal menghapus user. Akun mungkin sudah tidak tersedia atau masih terhubung dengan data lain.');
                }
                mysqli_stmt_close($stmt);
            } else {
                set_flash_message('danger', 'Permintaan penghapusan akun tidak dapat diproses.');
            }
        }
    }

    header('Location: data_user.php');
    exit();
}

$query = 'SELECT id, username, role FROM users ORDER BY role ASC, username ASC';
$result = mysqli_query($conn, $query);
$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

$total_user = count($users);
$total_admin = count(array_filter($users, static fn($row) => ($row['role'] ?? '') === 'admin'));
$total_guru = count(array_filter($users, static fn($row) => ($row['role'] ?? '') === 'guru'));
$total_siswa = count(array_filter($users, static fn($row) => ($row['role'] ?? '') === 'siswa'));
$data_user_error = !$result;

$e = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$role_labels = [
    'admin' => 'Administrator',
    'guru' => 'Guru',
    'siswa' => 'Siswa',
];
$role_pills = [
    'admin' => 'is-red',
    'guru' => 'is-blue',
    'siswa' => 'is-green',
];
$role_icons = [
    'admin' => 'bi-shield-lock-fill',
    'guru' => 'bi-person-workspace',
    'siswa' => 'bi-mortarboard-fill',
];

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/data_user_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
