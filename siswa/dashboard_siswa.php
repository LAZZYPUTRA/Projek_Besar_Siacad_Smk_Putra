<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';


check_role(['siswa']);

$user_id = $_SESSION['user_id'];

// Ambil data siswa berdasarkan user login
$query_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = '$user_id'");
$siswa = mysqli_fetch_assoc($query_siswa);

if (!$siswa) {
    die("Data profil siswa tidak ditemukan. Hubungi Administrator.");
}

$id_siswa   = $siswa['id'];
$nama_siswa = $siswa['nama'];
$nis        = $siswa['nis'];
$kelas      = $siswa['kelas'];

// Hitung statistik riil dari database
$query_stat = mysqli_query($conn, "SELECT COUNT(*) as total_nilai FROM nilai_rapor WHERE siswa_id = '$id_siswa'");
$stat = mysqli_fetch_assoc($query_stat);
$total_nilai = $stat['total_nilai'] ?? 0;

$query_mapel = mysqli_query($conn, "SELECT COUNT(*) as total_mapel FROM mengajar WHERE siswa_id = '$id_siswa'");
$stat_mapel = mysqli_fetch_assoc($query_mapel);
$total_mapel = $stat_mapel['total_mapel'] ?? 0;

// Ambil daftar nilai rapor riil untuk ringkasan
$query_nilai_list = mysqli_query($conn, "
    SELECT nr.*, m.kode_mapel, m.nama_mapel, g.nama AS nama_guru
    FROM nilai_rapor nr
    JOIN mapel m ON nr.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    WHERE nr.siswa_id = '$id_siswa'
    ORDER BY m.nama_mapel ASC
");

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="dashboard-wrapper">
    <!-- Banner Sambutan Sederhana & Jelas -->
    <div class="welcome-banner-clean">
        <div class="banner-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div>
            <h2>Selamat Datang, <?= htmlspecialchars($nama_siswa); ?></h2>
            <p>NIS: <?= htmlspecialchars($nis); ?> &bull; Kelas: <?= htmlspecialchars($kelas); ?></p>
        </div>
    </div>

    <!-- Statistik Berdasarkan Data Riil -->
    <div class="stats-grid-2col">
        <div class="stat-card-clean">
            <div class="stat-icon bg-blue"><i class="bi bi-journal-text"></i></div>
            <div>
                <span class="stat-label">Mata Pelajaran Diikuti</span>
                <h3 class="stat-val"><?= $total_mapel; ?> Mapel</h3>
            </div>
        </div>

        <div class="stat-card-clean">
            <div class="stat-icon bg-green"><i class="bi bi-award"></i></div>
            <div>
                <span class="stat-label">Nilai Rapor Masuk</span>
                <h3 class="stat-val"><?= $total_nilai; ?> Nilai</h3>
            </div>
        </div>
    </div>

    <!-- Tabel Ringkasan Nilai Rapor -->
    <div class="card-panel-clean">
        <div class="card-header-clean">
            <h3><i class="bi bi-table"></i> Ringkasan Nilai Rapor</h3>
            <a href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php" class="btn-link">Lihat Selengkapnya &rarr;</a>
        </div>
        <div class="card-body-clean">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>KKM</th>
                            <th>Pengetahuan</th>
                            <th>Keterampilan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_nilai_list) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query_nilai_list)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_mapel']); ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($row['kode_mapel']); ?></small>
                                    </td>
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
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data nilai rapor yang diinputkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>