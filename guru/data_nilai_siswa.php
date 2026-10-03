<?php
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['guru']);

$user_id = $_SESSION['user_id'];
// Cari data guru berdasarkan user_id login
$q_guru = mysqli_query($conn, "SELECT id FROM guru WHERE user_id = $user_id");
$guru = mysqli_fetch_assoc($q_guru);
$guru_id = $guru['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];
    
    if ($aksi === 'simpan_nilai') {
        $siswa_id   = (int)$_POST['siswa_id'];
        $mapel_id   = (int)$_POST['mapel_id'];
        $kkm        = (int)$_POST['nilai_kkm'];
        $np         = (int)$_POST['nilai_pengetahuan'];
        $nk         = (int)$_POST['nilai_keterampilan'];
        
        $rata = ($np + $nk) / 2;
        $ket  = ($rata >= $kkm) ? 'Tuntas' : 'Belum Tuntas';

        // Cek apakah sudah ada nilai
        $check = mysqli_query($conn, "SELECT id FROM nilai_rapor WHERE siswa_id=$siswa_id AND mapel_id=$mapel_id");
        if (mysqli_num_rows($check) > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE nilai_rapor SET nilai_kkm=?, nilai_pengetahuan=?, nilai_keterampilan=?, Keterangan=? WHERE siswa_id=? AND mapel_id=?");
            mysqli_stmt_bind_param($stmt, "iiisii", $kkm, $np, $nk, $ket, $siswa_id, $mapel_id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO nilai_rapor (siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, Keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiiis", $siswa_id, $mapel_id, $kkm, $np, $nk, $ket);
        }
        
        mysqli_stmt_execute($stmt) ? set_flash_message('success', 'Nilai berhasil disimpan!') : set_flash_message('danger', 'Gagal menyimpan nilai.');
        header('Location: data_nilai_siswa.php');
        exit();
    }
}

require_once __DIR__ . '/../includes/template_header.php';

// Ambil daftar siswa yang berada di mapel binaan guru login
$query = "
    SELECT s.id as siswa_id, s.nis, s.nama as nama_siswa, s.kelas, m.id as mapel_id, m.nama_mapel,
           nr.nilai_kkm, nr.nilai_pengetahuan, nr.nilai_keterampilan, nr.Keterangan
    FROM mengajar mg
    JOIN mapel m ON mg.mapel_id = m.id
    JOIN siswa s ON mg.siswa_id = s.id
    LEFT JOIN nilai_rapor nr ON (nr.siswa_id = s.id AND nr.mapel_id = m.id)
    WHERE m.guru_id = $guru_id
    ORDER BY m.nama_mapel ASC, s.kelas ASC, s.nama ASC
";
$result = mysqli_query($conn, $query);
?>

<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
    <h2><i class="bi bi-pencil-square text-success me-2"></i>Kelola Nilai Rapor Siswa</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Mapel</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>KKM</th>
                    <th>Pengetahuan</th>
                    <th>Keterampilan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_mapel']); ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']); ?> <br><small class="text-muted">NIS: <?= $row['nis']; ?></small></td>
                    <td><span class="badge bg-secondary"><?= $row['kelas']; ?></span></td>
                    <td><?= $row['nilai_kkm'] ?? '-'; ?></td>
                    <td><?= $row['nilai_pengetahuan'] ?? '-'; ?></td>
                    <td><?= $row['nilai_keterampilan'] ?? '-'; ?></td>
                    <td>
                        <?php if (isset($row['Keterangan'])): ?>
                            <span class="badge bg-<?= $row['Keterangan'] === 'Belum Tuntas' ? 'danger' : 'success'; ?>"><?= $row['Keterangan']; ?></span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Belum Diisi</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNilai<?= $row['siswa_id'] . '_' . $row['mapel_id']; ?>">
                            <i class="bi bi-pencil"></i> Input/Edit
                        </button>
                    </td>
                </tr>

                <!-- Modal Input Nilai -->
                <div class="modal fade" id="modalNilai<?= $row['siswa_id'] . '_' . $row['mapel_id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Form Nilai - <?= htmlspecialchars($row['nama_siswa']); ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="aksi" value="simpan_nilai">
                                    <input type="hidden" name="siswa_id" value="<?= $row['siswa_id']; ?>">
                                    <input type="hidden" name="mapel_id" value="<?= $row['mapel_id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nilai KKM</label>
                                        <input type="number" name="nilai_kkm" class="form-control" value="<?= $row['nilai_kkm'] ?? 75; ?>" min="0" max="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nilai Pengetahuan (0-100)</label>
                                        <input type="number" name="nilai_pengetahuan" class="form-control" value="<?= $row['nilai_pengetahuan'] ?? ''; ?>" min="0" max="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nilai Keterampilan (0-100)</label>
                                        <input type="number" name="nilai_keterampilan" class="form-control" value="<?= $row['nilai_keterampilan'] ?? ''; ?>" min="0" max="100" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Nilai</button>
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

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>