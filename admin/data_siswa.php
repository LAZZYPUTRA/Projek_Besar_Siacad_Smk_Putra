<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    // --- PROSES TAMBAH SISWA & AKUN ---
    if ($aksi === 'tambah') {
        $nis      = sanitize($_POST['nis']);
        $nama     = sanitize($_POST['nama']);
        $kelas    = sanitize($_POST['kelas']);
        $jk       = sanitize($_POST['jenis_kelamin']);
        $username = sanitize($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role     = 'siswa';

        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' UNION SELECT id FROM siswa WHERE nis='$nis'");
        if (mysqli_num_rows($cek) > 0) {
            set_flash_message('danger', 'Gagal! Username atau NIS sudah terdaftar.');
        } else {
            mysqli_begin_transaction($conn);
            try {
                $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt1, "sss", $username, $password, $role);
                mysqli_stmt_execute($stmt1);
                $user_id = mysqli_insert_id($conn);

                $stmt2 = mysqli_prepare($conn, "INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, "ssssi", $nis, $nama, $kelas, $jk, $user_id);
                mysqli_stmt_execute($stmt2);

                mysqli_commit($conn);
                set_flash_message('success', 'Data Siswa dan Akun berhasil ditambahkan!');
            } catch (Exception $e) {
                mysqli_rollback($conn);
                set_flash_message('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            }
        }
    } 
    // --- PROSES EDIT SISWA ---
    elseif ($aksi === 'edit') {
        $id_siswa = (int)$_POST['id_siswa'];
        $nis      = sanitize($_POST['nis']);
        $nama     = sanitize($_POST['nama']);
        $kelas    = sanitize($_POST['kelas']);
        $jk       = sanitize($_POST['jenis_kelamin']);

        $stmt = mysqli_prepare($conn, "UPDATE siswa SET nis=?, nama=?, kelas=?, jenis_kelamin=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nis, $nama, $kelas, $jk, $id_siswa);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Siswa berhasil diperbarui!');
        } else {
            set_flash_message('danger', 'Gagal memperbarui data Siswa.');
        }
    }
    // --- PROSES HAPUS SISWA ---
    elseif ($aksi === 'hapus') {
        $user_id = (int)$_POST['user_id'];
        
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Siswa beserta akun berhasil dihapus!');
        } else {
            set_flash_message('danger', 'Gagal menghapus data.');
        }
    }
    header("Location: data_siswa.php");
    exit();
}

require_once __DIR__ . '/../includes/template_header.php';

$query = "SELECT s.*, u.username FROM siswa s JOIN users u ON s.user_id = u.id ORDER BY s.kelas ASC, s.nama ASC";
$result = mysqli_query($conn, $query);
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
    <h2><i class="bi bi-person-badge text-success me-2"></i>Data Siswa</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>L/P</th>
                    <th>Username Akun</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nis']); ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['kelas']); ?></span></td>
                    <td><?= $row['jenis_kelamin']; ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['username']); ?></span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>"><i class="bi bi-pencil-square"></i> Edit</button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id']; ?>"><i class="bi bi-trash"></i> Hapus</button>
                    </td>
                </tr>

                <!-- Modal Edit Siswa -->
                <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Edit Data Siswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="aksi" value="edit">
                                    <input type="hidden" name="id_siswa" value="<?= $row['id']; ?>">
                                    <div class="mb-3">
                                        <label>NIS</label>
                                        <input type="text" class="form-control" name="nis" value="<?= htmlspecialchars($row['nis']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Nama Lengkap</label>
                                        <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($row['nama']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Kelas</label>
                                        <input type="text" class="form-control" name="kelas" value="<?= htmlspecialchars($row['kelas']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Jenis Kelamin</label>
                                        <select class="form-select" name="jenis_kelamin" required>
                                            <option value="L" <?= $row['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                            <option value="P" <?= $row['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Hapus Siswa -->
                <div class="modal fade" id="modalHapus<?= $row['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                                    <p>Hapus siswa <strong><?= htmlspecialchars($row['nama']); ?></strong> secara permanen?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah Siswa & Akun Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6>Data Siswa</h6>
                            <div class="mb-3"><label>NIS</label><input type="text" class="form-control" name="nis" required></div>
                            <div class="mb-3"><label>Nama Lengkap</label><input type="text" class="form-control" name="nama" required></div>
                            <div class="mb-3"><label>Kelas</label><input type="text" class="form-control" name="kelas" placeholder="Cth: XII RPL 1" required></div>
                            <div class="mb-3">
                                <label>Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Data Akun Login</h6>
                            <div class="mb-3"><label>Username</label><input type="text" class="form-control" name="username" required></div>
                            <div class="mb-3"><label>Password</label><input type="password" class="form-control" name="password" required></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>