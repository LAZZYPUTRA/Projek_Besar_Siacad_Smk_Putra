<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['guru']);

$id_user = $_SESSION['user_id'];
$query_guru = mysqli_query($conn, "SELECT id, nama FROM guru WHERE user_id = '$id_user'");
$data_guru = mysqli_fetch_assoc($query_guru);
$id_guru = $data_guru['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_nilai'])) {
    $siswa_id           = intval($_POST['siswa_id']);
    $mapel_id           = intval($_POST['mapel_id']);
    $nilai_kkm          = intval($_POST['nilai_kkm']);
    $nilai_pengetahuan  = intval($_POST['nilai_pengetahuan']);
    $nilai_keterampilan = intval($_POST['nilai_keterampilan']);

    $rata_rata = ($nilai_pengetahuan + $nilai_keterampilan) / 2;
    $keterangan = ($rata_rata >= $nilai_kkm) ? 'Tuntas' : 'Belum Tuntas';

    $cek_nilai = mysqli_query($conn, "SELECT id FROM nilai_rapor WHERE siswa_id = '$siswa_id' AND mapel_id = '$mapel_id'");

    if (mysqli_num_rows($cek_nilai) > 0) {
        $stmt = $conn->prepare("UPDATE nilai_rapor SET nilai_kkm = ?, nilai_pengetahuan = ?, nilai_keterampilan = ?, Keterangan = ? WHERE siswa_id = ? AND mapel_id = ?");
        $stmt->bind_param("iiiisi", $nilai_kkm, $nilai_pengetahuan, $nilai_keterampilan, $keterangan, $siswa_id, $mapel_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO nilai_rapor (siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, Keterangan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiis", $siswa_id, $mapel_id, $nilai_kkm, $nilai_pengetahuan, $nilai_keterampilan, $keterangan);
    }

    if ($stmt->execute()) {
        set_flash_message('success', 'Nilai rapor berhasil disimpan!');
    } else {
        set_flash_message('danger', 'Gagal menyimpan nilai rapor.');
    }
    $stmt->close();

    header("Location: " . BASE_URL . "guru/data_nilai_siswa.php");
    exit;
}

$query_mapel = mysqli_query($conn, "SELECT id, kode_mapel, nama_mapel FROM mapel WHERE guru_id = '$id_guru'");
$query_siswa = mysqli_query($conn, "SELECT id, nis, nama, kelas FROM siswa ORDER BY nama ASC");
$query_nilai = mysqli_query($conn, "
    SELECT nr.*, s.nama AS nama_siswa, s.nis, s.kelas, m.nama_mapel
    FROM nilai_rapor nr
    JOIN siswa s ON nr.siswa_id = s.id
    JOIN mapel m ON nr.mapel_id = m.id
    WHERE m.guru_id = '$id_guru'
    ORDER BY s.nama ASC
");

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="dashboard-columns">
    <div class="card-panel">
        <div class="card-header header-primary">
            <h5><i class="bi bi-pencil-square"></i> Input Nilai Rapor</h5>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-select" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php while ($m = mysqli_fetch_assoc($query_mapel)): ?>
                            <option value="<?= $m['id']; ?>">[<?= htmlspecialchars($m['kode_mapel']); ?>] <?= htmlspecialchars($m['nama_mapel']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Siswa</label>
                    <select name="siswa_id" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($s = mysqli_fetch_assoc($query_siswa)): ?>
                            <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['nis']) . ' - ' . htmlspecialchars($s['nama']) . ' (' . htmlspecialchars($s['kelas']) . ')'; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nilai KKM</label>
                    <input type="number" min="0" max="100" name="nilai_kkm" class="form-input" required placeholder="Contoh: 75">
                </div>

                <div class="form-group">
                    <label class="form-label">Nilai Pengetahuan</label>
                    <input type="number" min="0" max="100" name="nilai_pengetahuan" class="form-input" required placeholder="0 - 100">
                </div>

                <div class="form-group">
                    <label class="form-label">Nilai Keterampilan</label>
                    <input type="number" min="0" max="100" name="nilai_keterampilan" class="form-input" required placeholder="0 - 100">
                </div>

                <button type="submit" name="simpan_nilai" class="btn btn-primary full-width">
                    <i class="bi bi-save"></i> Simpan Nilai Rapor
                </button>
            </form>
        </div>
    </div>

    <div class="card-panel">
        <div class="card-header header-dark">
            <h5><i class="bi bi-table"></i> Daftar Nilai Rapor Siswa</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Mapel</th>
                            <th>KKM</th>
                            <th>Pengetahuan</th>
                            <th>Keterampilan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_nilai) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query_nilai)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_siswa']); ?></strong><br>
                                        <small class="text-muted">NIS: <?= htmlspecialchars($row['nis']); ?> | Kelas: <?= htmlspecialchars($row['kelas']); ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_mapel']); ?></td>
                                    <td><?= $row['nilai_kkm']; ?></td>
                                    <td><?= $row['nilai_pengetahuan']; ?></td>
                                    <td><?= $row['nilai_keterampilan']; ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($row['Keterangan']) === 'tuntas' ? 'success' : 'danger'; ?>">
                                            <?= htmlspecialchars($row['Keterangan']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada data nilai rapor.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>