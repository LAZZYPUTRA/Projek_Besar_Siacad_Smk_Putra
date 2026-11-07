<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $nip      = sanitize($_POST['nip']);
        $nama     = sanitize($_POST['nama']);
        $jk       = sanitize($_POST['jenis_kelamin']);
        $username = sanitize($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role     = 'guru';

        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' UNION SELECT id FROM guru WHERE nip='$nip'");
        if (mysqli_num_rows($cek) > 0) {
            set_flash_message('danger', 'Gagal! Username atau NIP sudah terdaftar.');
        } else {
            mysqli_begin_transaction($conn);
            try {
                $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt1, "sss", $username, $password, $role);
                mysqli_stmt_execute($stmt1);
                $user_id = mysqli_insert_id($conn);

                $stmt2 = mysqli_prepare($conn, "INSERT INTO guru (nip, nama, jenis_kelamin, user_id) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, "sssi", $nip, $nama, $jk, $user_id);
                mysqli_stmt_execute($stmt2);

                mysqli_commit($conn);
                set_flash_message('success', 'Data Guru dan Akun berhasil ditambahkan!');
            } catch (Exception $e) {
                mysqli_rollback($conn);
                set_flash_message('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            }
        }
    } 
    elseif ($aksi === 'edit') {
        $id_guru = (int)$_POST['id_guru'];
        $nip     = sanitize($_POST['nip']);
        $nama    = sanitize($_POST['nama']);
        $jk      = sanitize($_POST['jenis_kelamin']);

        $stmt = mysqli_prepare($conn, "UPDATE guru SET nip=?, nama=?, jenis_kelamin=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $nip, $nama, $jk, $id_guru);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Guru berhasil diperbarui!');
        } else {
            set_flash_message('danger', 'Gagal memperbarui data Guru.');
        }
    }
    elseif ($aksi === 'hapus') {
        $user_id = (int)$_POST['user_id'];
        
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Guru beserta akun berhasil dihapus!');
        } else {
            set_flash_message('danger', 'Gagal menghapus data.');
        }
    }

    header("Location: data_guru.php");
    exit();
}

require_once __DIR__ . '/../includes/template_header.php';

$query = "SELECT g.*, u.username FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC";
$result = mysqli_query($conn, $query);

$teachers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $teachers[] = $row;
}
?>

<!-- Header Halaman & Tombol Tambah -->
<div class="page-header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <i class="bi bi-person-workspace text-primary"></i> Manajemen Data Guru
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem; margin-bottom: 0;">Pusat pengelolaan data pendidik dan kredensial akun pengajar.</p>
    </div>
    <button type="button" class="btn-primary-custom" data-modal-target="#modalTambah">
        <i class="bi bi-plus-lg"></i> Tambah Guru Baru
    </button>
</div>

<!-- Tampilan Tabel Data Guru Full-Width -->
<div class="card-panel">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">NIP</th>
                    <th style="width: 32%;">Nama Guru & Gelar</th>
                    <th style="width: 12%;">Jenis Kelamin</th>
                    <th style="width: 18%;">Username Akun</th>
                    <th style="width: 15%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($teachers) > 0): ?>
                    <?php $no = 1; foreach ($teachers as $row): ?>
                    <tr>
                        <td><strong><?= $no++; ?></strong></td>
                        <td><?= htmlspecialchars($row['nip']); ?></td>
                        <td><strong><?= htmlspecialchars($row['nama']); ?></strong></td>
                        <td>
                            <span class="badge badge-primary">
                                <?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                            </span>
                        </td>
                        <td><span class="badge badge-secondary"><?= htmlspecialchars($row['username']); ?></span></td>
                        <td class="text-center">
                            <div class="table-action-btn" style="display: inline-flex; justify-content: center; gap: 0.35rem;">
                                <button type="button" class= "btn btn-sm btn-warning" data-modal-target="#modalEdit<?= $row['id']; ?>" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-modal-target="#modalHapus<?= $row['id']; ?>" title="Hapus Data">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="text-align: center; padding: 2rem;">Belum ada data guru terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= SEMUA MODAL DI LUAR TABEL ================= -->
<?php foreach ($teachers as $row): ?>

<!-- Modal Edit Guru -->
<div class="modal-overlay" id="modalEdit<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5>Edit Data Guru</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id_guru" value="<?= $row['id']; ?>">
                
                <div class="form-group">
                    <label class="form-label">NIP</label>
                    <input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($row['nip']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap & Gelar</label>
                    <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($row['nama']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-select" name="jenis_kelamin" required>
                        <option value="L" <?= $row['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?= $row['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Guru -->
<div class="modal-overlay" id="modalHapus<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-danger">
                <h5>Konfirmasi Hapus</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                <p>Hapus guru <strong><?= htmlspecialchars($row['nama']); ?></strong>?</p>
                <p style="font-size: 0.78rem; color: #dc2626; margin-top: 0.5rem;">Tindakan ini juga menghapus akun login terkait secara permanen.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger" style="background: #dc2626; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<?php endforeach; ?>

<!-- Modal Tambah Guru & Akun -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box modal-lg teacher-modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTambahTitle">
        <form method="POST" class="teacher-form">
            <div class="modal-header header-primary teacher-modal-header">
                <div class="teacher-modal-heading">
                    <span class="teacher-modal-icon" aria-hidden="true">
                        <i class="bi bi-person-plus-fill"></i>
                    </span>
                    <div class="teacher-modal-heading-copy">
                        <span class="teacher-modal-eyebrow">MANAJEMEN PENDIDIK</span>
                        <h5 id="modalTambahTitle">Tambah Guru Baru</h5>
                        <p>Lengkapi informasi guru dan akun untuk mengakses portal.</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn teacher-modal-close" data-modal-close aria-label="Tutup form">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>

            <div class="modal-body teacher-modal-body">
                <input type="hidden" name="aksi" value="tambah">

                <section class="teacher-form-section" aria-labelledby="teacherIdentityHeading">
                    <div class="teacher-section-heading">
                        <span class="teacher-section-icon" aria-hidden="true"><i class="bi bi-person-vcard"></i></span>
                        <div>
                            <h6 id="teacherIdentityHeading">Informasi Guru</h6>
                            <p>Masukkan identitas sesuai data resmi guru.</p>
                        </div>
                    </div>

                    <div class="teacher-form-grid">
                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherNip">Nomor Induk Pegawai <span class="teacher-required">Wajib</span></label>
                            <input type="text" class="form-control" id="teacherNip" name="nip" placeholder="Contoh: 198506152010011001" autocomplete="off" required>
                            <small class="teacher-field-help">Gunakan NIP yang valid dan belum terdaftar.</small>
                        </div>

                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherGender">Jenis Kelamin <span class="teacher-required">Wajib</span></label>
                            <select class="form-select" id="teacherGender" name="jenis_kelamin" required>
                                <option value="" disabled selected>Pilih jenis kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group teacher-field teacher-name-field">
                        <label class="form-label" for="teacherName">Nama Lengkap dan Gelar <span class="teacher-required">Wajib</span></label>
                        <input type="text" class="form-control" id="teacherName" name="nama" placeholder="Contoh: Budi Santoso, S.Kom." autocomplete="name" required>
                        <small class="teacher-field-help">Tuliskan nama beserta gelar jika ada.</small>
                    </div>
                </section>

                <section class="teacher-form-section teacher-account-section" aria-labelledby="teacherAccountHeading">
                    <div class="teacher-section-heading">
                        <span class="teacher-section-icon" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
                        <div>
                            <h6 id="teacherAccountHeading">Akun Login</h6>
                            <p>Kredensial ini digunakan guru untuk masuk ke sistem.</p>
                        </div>
                    </div>

                    <div class="teacher-form-grid">
                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherUsername">Username <span class="teacher-required">Wajib</span></label>
                            <input type="text" class="form-control" id="teacherUsername" name="username" placeholder="Buat username" autocomplete="off" required>
                            <small class="teacher-field-help">Username harus unik dan belum digunakan.</small>
                        </div>

                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherPassword">Kata Sandi <span class="teacher-required">Wajib</span></label>
                            <input type="password" class="form-control" id="teacherPassword" name="password" placeholder="Buat kata sandi" autocomplete="new-password" required>
                            <small class="teacher-field-help">Berikan kata sandi awal kepada guru secara aman.</small>
                        </div>
                    </div>
                </section>

                <p class="teacher-form-note"><i class="bi bi-info-circle" aria-hidden="true"></i> Pastikan seluruh informasi sudah benar sebelum menyimpan data.</p>
            </div>

            <div class="modal-footer teacher-modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-check2-circle" aria-hidden="true"></i> Simpan Data Guru
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>
</file>

<file path="admin/data_user.php">
<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

$logged_in_user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'reset_password') {
        $id_user   = (int)$_POST['id_user'];
        $pass_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $pass_baru, $id_user);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Password pengguna berhasil diperbarui!');
        } else {
            set_flash_message('danger', 'Gagal memperbarui password.');
        }
    } 
    elseif ($aksi === 'edit_user') {
        $id_user  = (int)$_POST['id_user'];
        $username = sanitize($_POST['username']);
        $role     = sanitize($_POST['role']);

        $stmt_cek = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmt_cek, "si", $username, $id_user);
        mysqli_stmt_execute($stmt_cek);
        $res_cek = mysqli_stmt_get_result($stmt_cek);

        if (mysqli_num_rows($res_cek) > 0) {
            set_flash_message('danger', 'Gagal! Username tersebut sudah digunakan oleh akun lain.');
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $username, $role, $id_user);
            
            if (mysqli_stmt_execute($stmt)) {
                if ($id_user === $logged_in_user_id) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role']     = $role;
                }
                set_flash_message('success', 'Data akun user berhasil diperbarui!');
            } else {
                set_flash_message('danger', 'Gagal memperbarui data user.');
            }
        }
    }
    elseif ($aksi === 'hapus_user') {
        $id_user = (int)$_POST['id_user'];

        if ($id_user === $logged_in_user_id) {
            set_flash_message('danger', 'Gagal! Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_user);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Akun user berhasil dihapus!');
            } else {
                set_flash_message('danger', 'Gagal menghapus user.');
            }
        }
    }

    header("Location: data_user.php");
    exit();
}

require_once __DIR__ . '/../includes/template_header.php';

$query = "SELECT id, username, role FROM users ORDER BY role ASC, username ASC";
$result = mysqli_query($conn, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>

<div class="page-header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <i class="bi bi-people text-primary"></i> Kelola Akun User
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem; margin-bottom: 0;">Kelola hak akses role, kredensial login, dan reset kata sandi pengguna.</p>
    </div>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Username</th>
                    <th style="width: 25%;">Role Hak Akses</th>
                    <th style="width: 40%;" class="text-center">Aksi Pengelolaan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php $no = 1; foreach ($users as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['username']); ?></strong>
                            <?php if ($row['id'] === $logged_in_user_id): ?>
                                <span style="font-size: 0.7rem; color: #166534; background: #dcfce7; padding: 2px 6px; border-radius: 4px; margin-left: 0.4rem; font-weight: 700;">(Anda)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $row['role'] === 'admin' ? 'danger' : ($row['role'] === 'guru' ? 'primary' : 'success'); ?>">
                                <?= strtoupper($row['role']); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <!-- Wrapper Flexbox agar tombol sejajar rapi ke samping -->
                            <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; flex-wrap: nowrap;">
                               <button type="button" class="btn btn-sm btn-secondary" data-modal-target="#modalEdit<?= $row['id']; ?>" title="Edit User">
                                    <i class="bi bi-key"></i> Edit
                                <button type="button" class="btn btn-sm btn-warning" data-modal-target="#modalReset<?= $row['id']; ?>" title="Reset Password">
                                    <i class="bi bi-key"></i> Reset
                                </button>
                                <?php if ($row['id'] !== $logged_in_user_id): ?>
                                    <button type="button" class="btn btn-sm btn-danger" data-modal-target="#modalHapus<?= $row['id']; ?>" title="Hapus User">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center" style="text-align: center; padding: 2rem;">Belum ada data user.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL DI LUAR TABEL ================= -->
<?php foreach ($users as $row): ?>

<!-- 1. Modal Edit User & Role -->
<div class="modal-overlay" id="modalEdit<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-primary">
                <h5>Edit Akun User - <?= htmlspecialchars($row['username']); ?></h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit_user">
                <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($row['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role Akses</label>
                    <select class="form-select" name="role" required>
                        <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : ''; ?>>ADMIN</option>
                        <option value="guru" <?= $row['role'] === 'guru' ? 'selected' : ''; ?>>GURU</option>
                        <option value="siswa" <?= $row['role'] === 'siswa' ? 'selected' : ''; ?>>SISWA</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Reset Password -->
<div class="modal-overlay" id="modalReset<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5>Reset Password - <?= htmlspecialchars($row['username']); ?></h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="reset_password">
                <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" class="form-control" name="password_baru" required minlength="4" placeholder="Masukkan password baru...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-warning" style="background: #d97706; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Simpan Password</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Hapus User -->
<?php if ($row['id'] !== $logged_in_user_id): ?>
<div class="modal-overlay" id="modalHapus<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-danger">
                <h5>Konfirmasi Hapus Akun</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="hapus_user">
                <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                <p>Apakah Anda yakin ingin menghapus akun user <strong><?= htmlspecialchars($row['username']); ?></strong>?</p>
                <p style="font-size: 0.78rem; color: #dc2626; margin-top: 0.5rem; margin-bottom: 0;">
                    <i class="bi bi-exclamation-triangle-fill"></i> Peringatan: Menghapus akun ini juga dapat menghapus data profil yang terhubung.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger" style="background: #dc2626; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>