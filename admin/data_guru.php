<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

// ==========================================
// PROSES FORM (CREATE, UPDATE, DELETE)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    // --- PROSES TAMBAH GURU & AKUN ---
    if ($aksi === 'tambah') {
        $nip      = sanitize($_POST['nip']);
        $nama     = sanitize($_POST['nama']);
        $jk       = sanitize($_POST['jenis_kelamin']);
        $username = sanitize($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role     = 'guru';

        // Cek duplikasi Username atau NIP
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' UNION SELECT id FROM guru WHERE nip='$nip'");
        if (mysqli_num_rows($cek) > 0) {
            set_flash_message('danger', 'Gagal! Username atau NIP sudah terdaftar.');
        } else {
            mysqli_begin_transaction($conn);
            try {
                // 1. Insert ke tabel users
                $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt1, "sss", $username, $password, $role);
                mysqli_stmt_execute($stmt1);
                $user_id = mysqli_insert_id($conn);

                // 2. Insert ke tabel guru
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
    // --- PROSES EDIT GURU ---
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
    // --- PROSES HAPUS GURU ---
    elseif ($aksi === 'hapus') {
        $user_id = (int)$_POST['user_id'];
        
        // Cukup hapus dari tabel users, tabel guru otomatis terhapus karena ON DELETE CASCADE
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Guru beserta akun berhasil dihapus!');
        } else {
            set_flash_message('danger', 'Gagal menghapus data.');
        }
    }

    // Pola PRG (Post/Redirect/Get) untuk mencegah resubmission form saat direfresh
    header("Location: data_guru.php");
    exit();
}

require_once __DIR__ . '/../includes/template_header.php';

// Ambil data guru beserta informasi akunnya
$query = "SELECT g.*, u.username FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC";
$result = mysqli_query($conn, $query);
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
    <h2><i class="bi bi-person-workspace text-primary me-2"></i>Data Guru</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Guru
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">NIP</th>
                    <th width="30%">Nama Guru</th>
                    <th width="15%">L/P</th>
                    <th width="15%">Username Akun</th>
                    <th width="20%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nip']); ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['username']); ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id']; ?>">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Data Guru</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="aksi" value="edit">
                                        <input type="hidden" name="id_guru" value="<?= $row['id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">NIP</label>
                                            <input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($row['nip']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($row['nama']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select class="form-select" name="jenis_kelamin" required>
                                                <option value="L" <?= $row['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="P" <?= $row['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus<?= $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                                        <p>Apakah Anda yakin ingin menghapus guru <strong><?= htmlspecialchars($row['nama']); ?></strong>?</p>
                                        <p class="text-danger small"><i class="bi bi-info-circle"></i> Menghapus guru juga akan menghapus akun login dan data terkait lainnya secara permanen.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Belum ada data guru.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Guru & Akun -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Data & Akun Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="text-primary mb-3">Data Diri Guru</h6>
                            <div class="mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control" name="nip" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin" required>
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Data Akun Login</h6>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-info-circle me-1"></i> Akun akan otomatis diberi role <b>Guru</b>.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>