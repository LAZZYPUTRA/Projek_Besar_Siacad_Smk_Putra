<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['guru']);

$id_user = $_SESSION['user_id'];

$query_guru = mysqli_query($conn, "SELECT * FROM guru WHERE user_id = '$id_user'");
$guru = mysqli_fetch_assoc($query_guru);
$id_guru = $guru['id'] ?? 0;
$nama_guru = $guru['nama'] ?? $_SESSION['username'];
$nip = $guru['nip'] ?? '-';

$q_mapel = mysqli_query($conn, "SELECT COUNT(*) as total FROM mapel WHERE guru_id = '$id_guru'");
$total_mapel = mysqli_fetch_assoc($q_mapel)['total'] ?? 0;

$q_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
$total_siswa = mysqli_fetch_assoc($q_siswa)['total'] ?? 0;

$q_nilai_input = mysqli_query($conn, "
    SELECT COUNT(nr.id) as total 
    FROM nilai_rapor nr 
    JOIN mapel m ON nr.mapel_id = m.id 
    WHERE m.guru_id = '$id_guru'
");
$total_nilai_input = mysqli_fetch_assoc($q_nilai_input)['total'] ?? 0;

$query_mapel_list = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel WHERE guru_id = '$id_guru'");

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="banner-card header-primary">
    <h2>Selamat Datang, <?= htmlspecialchars($nama_guru); ?>!</h2>
    <p>NIP: <?= htmlspecialchars($nip); ?> | Panel Pengelolaan Nilai Akademik Siswa (SIPN).</p>
</div>

<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-content">
            <span class="stat-title">Mapel Diampu</span>
            <span class="stat-value"><?= $total_mapel; ?></span>
        </div>
        <i class="bi bi-book stat-icon"></i>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-content">
            <span class="stat-title">Total Siswa</span>
            <span class="stat-value"><?= $total_siswa; ?></span>
        </div>
        <i class="bi bi-people stat-icon"></i>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-content">
            <span class="stat-title">Nilai Rapor Terinput</span>
            <span class="stat-value"><?= $total_nilai_input; ?></span>
        </div>
        <i class="bi bi-file-earmark-check stat-icon"></i>
    </div>
</div>

<div class="dashboard-columns">
    <div class="card-panel">
        <div class="card-header">
            <i class="bi bi-journal-bookmark"></i> Mata Pelajaran yang Diampu
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Mapel</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_mapel_list) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query_mapel_list)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><span class="badge badge-secondary"><?= htmlspecialchars($row['kode_mapel']); ?></span></td>
                                    <td><strong><?= htmlspecialchars($row['nama_mapel']); ?></strong></td>
                                    <td>
                                        <a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php" class="btn btn-sm btn-primary-outline">
                                            <i class="bi bi-pencil-square"></i> Input Nilai
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada mata pelajaran yang ditugaskan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-panel">
        <div class="card-header">
            <i class="bi bi-lightning-charge"></i> Akses Cepat
        </div>
        <div class="card-body">
            <div class="quick-links">
                <a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php" class="btn btn-primary action-btn">
                    <i class="bi bi-plus-circle"></i> Input Nilai Rapor
                </a>
                <a href="<?= BASE_URL; ?>profile.php" class="btn btn-secondary action-btn">
                    <i class="bi bi-person-gear"></i> Pengaturan Profil
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>