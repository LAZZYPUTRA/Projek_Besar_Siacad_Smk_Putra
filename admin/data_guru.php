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
    <div class="modal-box modal-lg" style="max-width: 650px;">
        <form method="POST">
            <div class="modal-header header-primary">
                <h5>Tambah Data & Akun Guru Baru</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">NIP</label>
                        <input type="text" class="form-control" name="nip" placeholder="Contoh: 1985..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-select" name="jenis_kelamin" required>
                            <option value="" disabled selected>Pilih...</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap & Gelar</label>
                    <input type="text" class="form-control" name="nama" placeholder="Contoh: Budi Santoso, S.Kom." required>
                </div>
                <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 1.25rem 0;">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Username Login</label>
                        <input type="text" class="form-control" name="username" placeholder="Buat username..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kata Sandi (Password)</label>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom">Simpan Data Guru</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>