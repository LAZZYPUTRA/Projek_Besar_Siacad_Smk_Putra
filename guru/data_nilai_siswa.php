<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['guru']);

$id_user = $_SESSION['user_id'];
$query_guru = mysqli_query($conn, "SELECT id, nama FROM guru WHERE user_id = '$id_user'");
$data_guru = mysqli_fetch_assoc($query_guru);
$id_guru = $data_guru['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Aksi Simpan / Edit Nilai
    if (isset($_POST['simpan_nilai'])) {
        $siswa_id           = intval($_POST['siswa_id']);
        $mapel_id           = intval($_POST['mapel_id']);
        $nilai_kkm          = intval($_POST['nilai_kkm']);
        $nilai_pengetahuan  = intval($_POST['nilai_pengetahuan']);
        $nilai_keterampilan = intval($_POST['nilai_keterampilan']);

        $rata_rata = ($nilai_pengetahuan + $nilai_keterampilan) / 2;
        $keterangan = ($rata_rata >= $nilai_kkm) ? 'Tuntas' : 'Belum Tuntas';

        // Logika Upsert: Update jika sudah ada, Insert jika belum ada
        $cek_nilai = mysqli_query($conn, "SELECT id FROM nilai_rapor WHERE siswa_id = '$siswa_id' AND mapel_id = '$mapel_id'");

        if (mysqli_num_rows($cek_nilai) > 0) {
            $stmt = $conn->prepare("UPDATE nilai_rapor SET nilai_kkm = ?, nilai_pengetahuan = ?, nilai_keterampilan = ?, Keterangan = ? WHERE siswa_id = ? AND mapel_id = ?");
            $stmt->bind_param("iiisii", $nilai_kkm, $nilai_pengetahuan, $nilai_keterampilan, $keterangan, $siswa_id, $mapel_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO nilai_rapor (siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, Keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiis", $siswa_id, $mapel_id, $nilai_kkm, $nilai_pengetahuan, $nilai_keterampilan, $keterangan);
        }

        if ($stmt->execute()) {
            set_flash_message('success', 'Data nilai rapor berhasil disimpan!');
        } else {
            set_flash_message('danger', 'Gagal menyimpan nilai rapor.');
        }
        $stmt->close();

        header("Location: " . BASE_URL . "guru/data_nilai_siswa.php");
        exit;
    } 
    // 2. Aksi Hapus Nilai
    elseif (isset($_POST['aksi']) && $_POST['aksi'] === 'hapus_nilai') {
        $id_nilai = intval($_POST['id_nilai']);

        // Verifikasi pengaman: hapus nilai hanya jika mapel milik guru yang sedang login
        $stmt = $conn->prepare("
            DELETE nr FROM nilai_rapor nr
            JOIN mapel m ON nr.mapel_id = m.id
            WHERE nr.id = ? AND m.guru_id = ?
        ");
        $stmt->bind_param("ii", $id_nilai, $id_guru);

        if ($stmt->execute()) {
            set_flash_message('success', 'Data nilai rapor berhasil dihapus!');
        } else {
            set_flash_message('danger', 'Gagal menghapus data nilai.');
        }
        $stmt->close();

        header("Location: " . BASE_URL . "guru/data_nilai_siswa.php");
        exit;
    }
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

$mapel_list = [];
while ($m = mysqli_fetch_assoc($query_mapel)) { $mapel_list[] = $m; }

$siswa_list = [];
while ($s = mysqli_fetch_assoc($query_siswa)) { $siswa_list[] = $s; }

$nilai_list = [];
while ($n = mysqli_fetch_assoc($query_nilai)) { $nilai_list[] = $n; }

require_once __DIR__ . '/../includes/template_header.php';
?>

<!-- Header Halaman & Tombol Tambah -->
<div class="page-header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <i class="bi bi-pencil-square text-primary"></i> Kelola Nilai Rapor
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem; margin-bottom: 0;">Input, perbarui, dan kelola capaian nilai peserta didik.</p>
    </div>
    <button type="button" class="btn-primary-custom" data-modal-target="#modalTambahNilai">
        <i class="bi bi-plus-lg"></i> Input Nilai Baru
    </button>
</div>

<!-- Tampilan Tabel Nilai Full-Width -->
<div class="card-panel">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">NIS</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th class="text-center" style="width: 8%;">Kelas</th>
                    <th style="width: 15%;">Mata Pelajaran</th>
                    <th class="text-center" style="width: 8%;">KKM</th>
                    <th class="text-center" style="width: 8%;">P.Tahuan</th>
                    <th class="text-center" style="width: 8%;">K.Trampilan</th>
                    <th class="text-center" style="width: 10%;">Status</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($nilai_list) > 0): ?>
                    <?php $no = 1; foreach ($nilai_list as $row): ?>
                        <tr>
                            <td><strong><?= $no++; ?></strong></td>
                            <td>
                                <span class="badge badge-secondary" style="font-family: monospace; font-size: 0.85rem; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($row['nis']); ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--text-dark);"><?= htmlspecialchars($row['nama_siswa']); ?></strong>
                            </td>
                            <td class="text-center">
                                <strong><?= htmlspecialchars($row['kelas']); ?></strong>
                            </td>
                            <td><?= htmlspecialchars($row['nama_mapel']); ?></td>
                            <td class="text-center"><span class="badge badge-secondary"><?= $row['nilai_kkm']; ?></span></td>
                            <td class="text-center" style="font-weight: 800; font-size: 1rem; color: var(--text-dark);"><?= $row['nilai_pengetahuan']; ?></td>
                            <td class="text-center" style="font-weight: 800; font-size: 1rem; color: var(--text-dark);"><?= $row['nilai_keterampilan']; ?></td>
                            <td class="text-center">
                                <span class="badge badge-<?= strtolower($row['Keterangan']) === 'tuntas' ? 'success' : 'danger'; ?>">
                                    <?= htmlspecialchars($row['Keterangan']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                    <button type="button" class="btn btn-sm btn-warning" data-modal-target="#modalEditNilai<?= $row['id']; ?>" title="Edit Nilai">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-modal-target="#modalHapusNilai<?= $row['id']; ?>" title="Hapus Nilai">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted" style="padding: 3rem;">
                            <i class="bi bi-folder2-open" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            Belum ada data nilai rapor yang diinputkan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL TAMBAH NILAI BARU ================= -->
<div class="modal-overlay" id="modalTambahNilai">
    <div class="modal-box modal-lg" style="max-width: 600px;">
        <form method="POST">
            <div class="modal-header header-primary">
                <h5>Input Nilai Rapor Siswa</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapel_list as $m): ?>
                            <option value="<?= $m['id']; ?>">[<?= htmlspecialchars($m['kode_mapel']); ?>] <?= htmlspecialchars($m['nama_mapel']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Peserta Didik (Siswa)</label>
                    <select name="siswa_id" class="form-control" required>
                        <option value="" disabled selected>-- Cari & Pilih Siswa --</option>
                        <?php foreach ($siswa_list as $s): ?>
                            <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['nis']) . ' - ' . htmlspecialchars($s['nama']) . ' (' . htmlspecialchars($s['kelas']) . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 1.5rem 0;">

                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Batas KKM</label>
                        <input type="number" min="0" max="100" name="nilai_kkm" class="form-control" required placeholder="Cth: 75">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nilai Pengetahuan</label>
                        <input type="number" min="0" max="100" name="nilai_pengetahuan" class="form-control" required placeholder="0-100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nilai Keterampilan</label>
                        <input type="number" min="0" max="100" name="nilai_keterampilan" class="form-control" required placeholder="0-100">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" name="simpan_nilai" class="btn-primary-custom">
                    <i class="bi bi-save"></i> Simpan Data Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT & HAPUS NILAI (PER BARIS TABEL) ================= -->
<?php foreach ($nilai_list as $row): ?>

<!-- 1. Modal Edit Nilai -->
<div class="modal-overlay" id="modalEditNilai<?= $row['id']; ?>">
    <div class="modal-box modal-lg" style="max-width: 600px;">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5>Edit Nilai Rapor - <?= htmlspecialchars($row['nama_siswa']); ?></h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="mapel_id" value="<?= $row['mapel_id']; ?>">
                <input type="hidden" name="siswa_id" value="<?= $row['siswa_id']; ?>">

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Mata Pelajaran</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_mapel']); ?>" disabled style="background: var(--bg-surface);">
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Peserta Didik</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['nis']) . ' - ' . htmlspecialchars($row['nama_siswa']); ?>" disabled style="background: var(--bg-surface);">
                </div>

                <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 1.5rem 0;">

                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Batas KKM</label>
                        <input type="number" min="0" max="100" name="nilai_kkm" class="form-control" value="<?= $row['nilai_kkm']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nilai Pengetahuan</label>
                        <input type="number" min="0" max="100" name="nilai_pengetahuan" class="form-control" value="<?= $row['nilai_pengetahuan']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nilai Keterampilan</label>
                        <input type="number" min="0" max="100" name="nilai_keterampilan" class="form-control" value="<?= $row['nilai_keterampilan']; ?>" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" name="simpan_nilai" class="btn-warning">
                    <i class="bi bi-save"></i> Perbarui Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Hapus Nilai -->
<div class="modal-overlay" id="modalHapusNilai<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-danger">
                <h5>Konfirmasi Hapus Nilai</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="hapus_nilai">
                <input type="hidden" name="id_nilai" value="<?= $row['id']; ?>">
                <p>Apakah Anda yakin ingin menghapus data nilai milik <strong><?= htmlspecialchars($row['nama_siswa']); ?></strong> pada mapel <strong><?= htmlspecialchars($row['nama_mapel']); ?></strong>?</p>
                <p style="font-size: 0.78rem; color: #dc2626; margin-top: 0.5rem; margin-bottom: 0;">
                    <i class="bi bi-exclamation-triangle-fill"></i> Data nilai yang terhapus tidak dapat dikembalikan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>