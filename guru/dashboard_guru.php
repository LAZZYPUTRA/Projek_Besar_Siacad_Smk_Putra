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

$query_mapel_list = mysqli_query($conn, "SELECT id, kode_mapel, nama_mapel FROM mapel WHERE guru_id = '$id_guru'");

require_once __DIR__ . '/../includes/template_header.php';
?>

<!-- Header Halaman & Tombol Aksi Utama -->
<div class="page-header-action" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <i class="bi bi-person-badge text-primary"></i> Selamat Datang, <?= htmlspecialchars($nama_guru); ?>!
        </h2>
    
    </div>
    <a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php" class="btn-primary-custom">
        <i class="bi bi-pencil-square"></i> Kelola & Input Nilai Rapor
    </a>
</div>

<!-- Grid Ringkasan Statistik -->
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card stat-primary">
        <div class="stat-content">
            <span class="stat-title">Mapel Diampu</span>
            <span class="stat-value"><?= $total_mapel; ?></span>
        </div>
        <i class="bi bi-journal-bookmark stat-icon"></i>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-content">
            <span class="stat-title">Total Siswa Terdaftar</span>
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

<!-- Tabel Mata Pelajaran Diampu Full-Width (Gaya Admin) -->
<div class="card-panel">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-dark); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-journal-check text-primary"></i> Daftar Mata Pelajaran Diampu
        </h3>
        <span class="badge badge-primary"><?= $total_mapel; ?> Mapel</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Kode Mapel</th>
                    <th style="width: 55%;">Nama Mata Pelajaran</th>
                    <th style="width: 20%;" class="text-center">Aksi Pengelolaan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($query_mapel_list) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($query_mapel_list)): ?>
                        <tr>
                            <td><strong><?= $no++; ?></strong></td>
                            <td>
                                <span class="badge badge-secondary" style="font-family: monospace; font-size: 0.85rem;">
                                    <?= htmlspecialchars($row['kode_mapel']); ?>
                                </span>
                            </td>
                            <td><strong style="color: var(--text-dark);"><?= htmlspecialchars($row['nama_mapel']); ?></strong></td>
                            <td class="text-center">
                                <a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php" class="btn btn-sm btn-warning" title="Input Nilai">
                                    <i class="bi bi-pencil-square"></i> Input Nilai
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted" style="padding: 3rem;">
                            <i class="bi bi-journal-x" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            Belum ada mata pelajaran yang ditugaskan kepada Anda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>