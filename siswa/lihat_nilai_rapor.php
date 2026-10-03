<?php
// 1. Panggil file koneksi database TERLEBIH DAHULU
require_once __DIR__ . '/../config/koneksi_database.php';
// 2. Panggil helper untuk cek login dan hak akses
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['siswa']);

$user_id = $_SESSION['user_id'];

// Ambil data detail siswa berdasarkan user_id yang sedang login
$q_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = $user_id");

// Proteksi jika data profil siswa belum terhubung ke user ini
if (!$q_siswa || mysqli_num_rows($q_siswa) === 0) {
    die("<div style='padding: 20px; text-align: center; font-family: sans-serif;'>
            <h3>Error: Profil Siswa Tidak Ditemukan!</h3>
            <p>Akun ini belum memiliki data profil siswa yang valid di database.</p>
            <a href='../auth/logout.php'>Kembali ke Login</a>
         </div>");
}

$siswa = mysqli_fetch_assoc($q_siswa);
$siswa_id = $siswa['id'];

// Ambil daftar nilai milik siswa ini
$query = "
    SELECT m.kode_mapel, m.nama_mapel, g.nama as nama_guru, 
           nr.nilai_kkm, nr.nilai_pengetahuan, nr.nilai_keterampilan, nr.Keterangan
    FROM mengajar mg
    JOIN mapel m ON mg.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    LEFT JOIN nilai_rapor nr ON (nr.siswa_id = mg.siswa_id AND nr.mapel_id = m.id)
    WHERE mg.siswa_id = $siswa_id
";
$result = mysqli_query($conn, $query);

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h4><i class="bi bi-person-badge text-primary me-2"></i>Rapor Digital Siswa</h4>
        <div class="row mt-3">
            <div class="col-md-4"><strong>Nama:</strong> <?= htmlspecialchars($siswa['nama']); ?></div>
            <div class="col-md-4"><strong>NIS:</strong> <?= htmlspecialchars($siswa['nis']); ?></div>
            <div class="col-md-4"><strong>Kelas:</strong> <?= htmlspecialchars($siswa['kelas']); ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Kode</th>
                    <th width="25%">Mata Pelajaran</th>
                    <th width="20%">Guru Pengampu</th>
                    <th width="8%">KKM</th>
                    <th width="10%">Pengetahuan</th>
                    <th width="10%">Keterampilan</th>
                    <th width="12%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['kode_mapel']); ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_mapel']); ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                        <td class="text-center"><?= $row['nilai_kkm'] ?? '-'; ?></td>
                        <td class="text-center"><?= $row['nilai_pengetahuan'] ?? '-'; ?></td>
                        <td class="text-center"><?= $row['nilai_keterampilan'] ?? '-'; ?></td>
                        <td>
                            <?php if (isset($row['Keterangan'])): ?>
                                <span class="badge bg-<?= $row['Keterangan'] === 'Belum Tuntas' ? 'danger' : 'success'; ?>">
                                    <?= htmlspecialchars($row['Keterangan']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Belum Diisi</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">Belum ada data mata pelajaran yang diikuti.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>