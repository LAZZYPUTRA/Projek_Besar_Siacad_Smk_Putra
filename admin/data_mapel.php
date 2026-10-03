<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $kode = sanitize($_POST['kode_mapel']);
        $nama = sanitize($_POST['nama_mapel']);
        $guru = (int)$_POST['guru_id'];

        $stmt = mysqli_prepare($conn, "INSERT INTO mapel (kode_mapel, nama_mapel, guru_id) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $kode, $nama, $guru);
        mysqli_stmt_execute($stmt) ? set_flash_message('success', 'Mapel berhasil ditambahkan!') : set_flash_message('danger', 'Gagal menambah mapel.');
    } 
    elseif ($aksi === 'edit') {
        $id_mapel = (int)$_POST['id_mapel'];
        $kode     = sanitize($_POST['kode_mapel']);
        $nama     = sanitize($_POST['nama_mapel']);
        $guru     = (int)$_POST['guru_id'];

        $stmt = mysqli_prepare($conn, "UPDATE mapel SET kode_mapel=?, nama_mapel=?, guru_id=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssii", $kode, $nama, $guru, $id_mapel);
        mysqli_stmt_execute($stmt) ? set_flash_message('success', 'Mapel berhasil diperbarui!') : set_flash_message('danger', 'Gagal memperbarui.');
    }
    elseif ($aksi === 'hapus') {
        $id_mapel = (int)$_POST['id_mapel'];
        $stmt = mysqli_prepare($conn, "DELETE FROM mapel WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id_mapel);
        mysqli_stmt_execute($stmt) ? set_flash_message('success', 'Mapel dihapus!') : set_flash_message('danger', 'Gagal menghapus.');
    }
    header("Location: data_mapel.php");
    exit();
}

require_once __DIR__ . '/../includes/template_header.php';

// Ambil data mapel beserta nama gurunya
$query = "SELECT m.*, g.nama as nama_guru FROM mapel m JOIN guru g ON m.guru_id = g.id ORDER BY m.kode_mapel ASC";
$result = mysqli_query($conn, $query);

// Ambil daftar guru untuk dropdown
$guru_list = mysqli_query($conn, "SELECT id, nama FROM guru ORDER BY nama ASC");
$options_guru = "";
while ($g = mysqli_fetch_assoc($guru_list)) {
    $options_guru .= "<option value='{$g['id']}'>{$g['nama']}</option>";
}
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
    <h2><i class="bi bi-book text-warning me-2"></i>Data Mata Pelajaran</h2>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Mapel
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode</th>
                    <th width="35%">Nama Mata Pelajaran</th>
                    <th width="30%">Guru Pengampu</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['kode_mapel']); ?></span></td>
                    <td><?= htmlspecialchars($row['nama_mapel']); ?></td>
                    <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id']; ?>"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>

                <!-- Modal Edit Mapel -->
                <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Edit Mapel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="aksi" value="edit">
                                    <input type="hidden" name="id_mapel" value="<?= $row['id']; ?>">
                                    <div class="mb-3"><label>Kode Mapel</label><input type="text" class="form-control" name="kode_mapel" value="<?= htmlspecialchars($row['kode_mapel']); ?>" required></div>
                                    <div class="mb-3"><label>Nama Mapel</label><input type="text" class="form-control" name="nama_mapel" value="<?= htmlspecialchars($row['nama_mapel']); ?>" required></div>
                                    <div class="mb-3">
                                        <label>Guru Pengampu</label>
                                        <select class="form-select" name="guru_id" required>
                                            <?php 
                                            // Reset pointer query guru untuk dropdown
                                            mysqli_data_seek($guru_list, 0);
                                            while($g = mysqli_fetch_assoc($guru_list)): 
                                            ?>
                                                <option value="<?= $g['id'] ?>" <?= $row['guru_id'] == $g['id'] ? 'selected' : '' ?>><?= $g['nama'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-warning">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Hapus Mapel -->
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
                                    <input type="hidden" name="id_mapel" value="<?= $row['id']; ?>">
                                    <p>Hapus mapel <strong><?= htmlspecialchars($row['nama_mapel']); ?></strong>?</p>
                                </div>
                                <div class="modal-footer">
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

<!-- Modal Tambah Mapel -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Tambah Mapel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="mb-3"><label>Kode Mapel</label><input type="text" class="form-control" name="kode_mapel" required></div>
                    <div class="mb-3"><label>Nama Mapel</label><input type="text" class="form-control" name="nama_mapel" required></div>
                    <div class="mb-3">
                        <label>Guru Pengampu</label>
                        <select class="form-select" name="guru_id" required>
                            <option value="" disabled selected>-- Pilih Guru --</option>
                            <?= $options_guru; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>