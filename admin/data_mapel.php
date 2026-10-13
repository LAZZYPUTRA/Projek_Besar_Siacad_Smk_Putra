<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
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

$query = "SELECT m.*, g.nama as nama_guru FROM mapel m JOIN guru g ON m.guru_id = g.id ORDER BY m.kode_mapel ASC";
$result = mysqli_query($conn, $query);

$guru_list = mysqli_query($conn, "SELECT id, nama FROM guru ORDER BY nama ASC");
$options_guru = "";
while ($g = mysqli_fetch_assoc($guru_list)) {
    $options_guru .= "<option value='{$g['id']}'>{$g['nama']}</option>";
}
?>

<div class="page-header flex-between">
    <h2><i class="bi bi-book"></i> Data Mata Pelajaran</h2>
    <button class="btn btn-warning" data-modal-target="#modalTambah">
        <i class="bi bi-plus-circle"></i> Tambah Mapel
    </button>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Kode</th>
                    <th style="width: 35%;">Nama Mata Pelajaran</th>
                    <th style="width: 30%;">Guru Pengampu</th>
                    <th style="width: 15%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><span class="badge badge-secondary"><?= htmlspecialchars($row['kode_mapel']); ?></span></td>
                    <td><?= htmlspecialchars($row['nama_mapel']); ?></td>
                    <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" data-modal-target="#modalEdit<?= $row['id']; ?>"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger" data-modal-target="#modalHapus<?= $row['id']; ?>"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>

                <!-- Modal Edit Mapel -->
                <div class="modal-overlay" id="modalEdit<?= $row['id']; ?>">
                    <div class="modal-box">
                        <form method="POST">
                            <div class="modal-header header-warning">
                                <h5>Edit Mapel</h5>
                                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="aksi" value="edit">
                                <input type="hidden" name="id_mapel" value="<?= $row['id']; ?>">
                                <div class="form-group"><label class="form-label">Kode Mapel</label><input type="text" class="form-input" name="kode_mapel" value="<?= htmlspecialchars($row['kode_mapel']); ?>" required></div>
                                <div class="form-group"><label class="form-label">Nama Mapel</label><input type="text" class="form-input" name="nama_mapel" value="<?= htmlspecialchars($row['nama_mapel']); ?>" required></div>
                                <div class="form-group">
                                    <label class="form-label">Guru Pengampu</label>
                                    <select class="form-select" name="guru_id" required>
                                        <?php 
                                        mysqli_data_seek($guru_list, 0);
                                        while($g = mysqli_fetch_assoc($guru_list)): 
                                        ?>
                                            <option value="<?= $g['id'] ?>" <?= $row['guru_id'] == $g['id'] ? 'selected' : '' ?>><?= $g['nama'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                                <button type="submit" class="btn btn-warning">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Hapus Mapel -->
                <div class="modal-overlay" id="modalHapus<?= $row['id']; ?>">
                    <div class="modal-box">
                        <form method="POST">
                            <div class="modal-header header-danger">
                                <h5>Konfirmasi Hapus</h5>
                                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id_mapel" value="<?= $row['id']; ?>">
                                <p>Hapus mapel <strong><?= htmlspecialchars($row['nama_mapel']); ?></strong>?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Mapel -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5>Tambah Mapel</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-group"><label class="form-label">Kode Mapel</label><input type="text" class="form-input" name="kode_mapel" required></div>
                <div class="form-group"><label class="form-label">Nama Mapel</label><input type="text" class="form-input" name="nama_mapel" required></div>
                <div class="form-group">
                    <label class="form-label">Guru Pengampu</label>
                    <select class="form-select" name="guru_id" required>
                        <option value="" disabled selected>-- Pilih Guru --</option>
                        <?= $options_guru; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-warning">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>