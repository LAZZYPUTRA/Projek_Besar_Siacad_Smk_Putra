<?php
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $mapel = (int)$_POST['mapel_id'];
        $siswa = (int)$_POST['siswa_id'];

        $stmt = mysqli_prepare($conn, "INSERT INTO mengajar (mapel_id, siswa_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $mapel, $siswa);
        mysqli_stmt_execute($stmt) ? set_flash_message('success', 'Penugasan mengajar ditambahkan!') : set_flash_message('danger', 'Gagal menyimpan data.');
    } 
    elseif ($aksi === 'hapus') {
        $id_mengajar = (int)$_POST['id_mengajar'];
        $stmt = mysqli_prepare($conn, "DELETE FROM mengajar WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id_mengajar);
        mysqli_stmt_execute($stmt) ? set_flash_message('success', 'Penugasan dihapus!') : set_flash_message('danger', 'Gagal menghapus.');
    }
    header("Location: data_mengajar.php");
    exit();
}

require_once __DIR__ . '/../includes/template_header.php';

// Ambil data mengajar beserta nama mapel, guru, dan siswa
$query = "
    SELECT mg.id, m.nama_mapel, m.kode_mapel, g.nama as nama_guru, s.nama as nama_siswa, s.kelas 
    FROM mengajar mg
    JOIN mapel m ON mg.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    JOIN siswa s ON mg.siswa_id = s.id
    ORDER BY m.nama_mapel ASC, s.kelas ASC, s.nama ASC
";
$result = mysqli_query($conn, $query);

// Data Dropdown
$opt_mapel = "";
$q_mapel = mysqli_query($conn, "SELECT id, nama_mapel, kode_mapel FROM mapel ORDER BY nama_mapel ASC");
while ($m = mysqli_fetch_assoc($q_mapel)) { 
    $opt_mapel .= "<option value='{$m['id']}'>[{$m['kode_mapel']}] {$m['nama_mapel']}</option>"; 
}

$opt_siswa = "";
$q_siswa = mysqli_query($conn, "SELECT id, nama, kelas FROM siswa ORDER BY kelas ASC, nama ASC");
while ($s = mysqli_fetch_assoc($q_siswa)) { 
    $opt_siswa .= "<option value='{$s['id']}'>{$s['nama']} - Kelas {$s['kelas']}</option>"; 
}
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
    <h2><i class="bi bi-card-checklist text-primary me-2"></i>Data Penugasan Kelas (Mengajar)</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tugaskan Siswa
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Mata Pelajaran</th>
                    <th width="25%">Guru Pengampu</th>
                    <th width="25%">Nama Siswa</th>
                    <th width="10%">Kelas</th>
                    <th width="10%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_mapel']); ?></strong> <br><small class="text-muted"><?= htmlspecialchars($row['kode_mapel']); ?></small></td>
                        <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                        <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['kelas']); ?></span></td>
                        <td class="text-center">
                            <form method="POST" onsubmit="return confirm('Hapus penugasan ini?');" class="d-inline">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id_mengajar" value="<?= $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Belum ada data penugasan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Penugasan -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tugaskan Siswa ke Mapel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select class="form-select" name="mapel_id" required>
                            <option value="" disabled selected>-- Pilih Mapel --</option>
                            <?= $opt_mapel; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Siswa</label>
                        <select class="form-select" name="siswa_id" required>
                            <option value="" disabled selected>-- Pilih Siswa --</option>
                            <?= $opt_siswa; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>