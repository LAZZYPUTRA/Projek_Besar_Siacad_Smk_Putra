<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['siswa']);

$user_id = $_SESSION['user_id'];

// Ambil data siswa berdasarkan user login[cite: 5]
$query_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = '$user_id'");
$siswa = mysqli_fetch_assoc($query_siswa);

if (!$siswa) {
    die("Data profil siswa tidak ditemukan. Hubungi Administrator.");
}

$id_siswa   = $siswa['id'];
$nama_siswa = $siswa['nama'];
$nis        = $siswa['nis'];
$kelas      = $siswa['kelas'];

// Hitung statistik riil dari database[cite: 5]
$query_stat = mysqli_query($conn, "SELECT COUNT(*) as total_nilai FROM nilai_rapor WHERE siswa_id = '$id_siswa'");
$stat = mysqli_fetch_assoc($query_stat);
$total_nilai = $stat['total_nilai'] ?? 0;

$query_mapel = mysqli_query($conn, "SELECT COUNT(*) as total_mapel FROM mengajar WHERE siswa_id = '$id_siswa'");
$stat_mapel = mysqli_fetch_assoc($query_mapel);
$total_mapel = $stat_mapel['total_mapel'] ?? 0;

// Ambil daftar nilai rapor riil untuk ringkasan[cite: 5]
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

<div class="dashboard-wrapper" style="display: flex; flex-direction: column; gap: 1.5rem;">
    
    <!-- Hero Banner Profil Akademik Siswa (Enterprise Style) -->
    <div style="background: linear-gradient(135deg, var(--primary-dark) 0%, #1e40af 100%); color: #ffffff; padding: 2rem; border-radius: 16px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.8;">Portal Akademik Peserta Didik</span>
                <h2 style="font-size: 1.4rem; font-weight: 800; margin-top: 2px; color: #ffffff;"><?= htmlspecialchars($nama_siswa); ?></h2>
                <p style="font-size: 0.85rem; opacity: 0.9; margin-top: 4px;">NIS: <?= htmlspecialchars($nis); ?> &bull; Rombel Kelas: <strong><?= htmlspecialchars($kelas); ?></strong></p>
            </div>
        </div>
        <div style="display: none; @media(min-width: 768px){display: block;}">
            <a href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php" class="btn" style="background: rgba(255,255,255,0.2); color: #ffffff; border: 1px solid rgba(255,255,255,0.3); font-weight: 700; font-size: 0.85rem; padding: 0.6rem 1rem; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-file-earmark-text"></i> Buka Transkrip Lengkap
            </a>
        </div>
    </div>

    <!-- Statistik Ringkas Berbasis Data Riil -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
        <div class="stat-card-clean" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px;">
            <div class="stat-icon bg-blue" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.1); color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div>
                <span class="stat-label" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Mata Pelajaran Diikuti</span>
                <h3 class="stat-val" style="font-size: 1.4rem; font-weight: 800; color: var(--text-dark); margin-top: 2px;"><?= $total_mapel; ?> <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Mapel</span></h3>
            </div>
        </div>

        <div class="stat-card-clean" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px;">
            <div class="stat-icon bg-green" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1); color: #059669; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;"><i class="bi bi-award-fill"></i></div>
            <div>
                <span class="stat-label" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Nilai Rapor Masuk</span>
                <h3 class="stat-val" style="font-size: 1.4rem; font-weight: 800; color: var(--text-dark); margin-top: 2px;"><?= $total_nilai; ?> <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Capaian</span></h3>
            </div>
        </div>
    </div>

    <!-- Tabel Ringkasan Nilai Rapor -->
    <div class="card-panel-clean" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <div class="card-header-clean" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-table text-primary"></i> Ringkasan Nilai Capaian Kompetensi Rapor
            </h3>
            <a href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php" class="btn-link" style="font-size: 0.82rem; font-weight: 700; color: var(--primary-dark); text-decoration: none; display: flex; align-items: center; gap: 0.3rem;">
                Lihat Selengkapnya <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body-clean" style="padding: 1.5rem;">
            <div class="table-responsive">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">No</th>
                            <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Mata Pelajaran</th>
                            <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">KKM</th>
                            <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Pengetahuan</th>
                            <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Keterampilan</th>
                            <th style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_nilai_list) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query_nilai_list)): ?>
                                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;">
                                    <td style="padding: 0.85rem; font-weight: 600; color: var(--text-muted);"><?= $no++; ?></td>
                                    <td style="padding: 0.85rem;">
                                        <strong style="color: var(--text-dark);"><?= htmlspecialchars($row['nama_mapel']); ?></strong>
                                        <br><small style="color: var(--text-muted); font-family: monospace; font-size: 0.75rem;"><?= htmlspecialchars($row['kode_mapel']); ?></small>
                                    </td>
                                    <td style="padding: 0.85rem; font-weight: 600; color: var(--text-dark);"><?= $row['nilai_kkm']; ?></td>
                                    <td style="padding: 0.85rem; font-weight: 700; color: var(--text-dark);"><?= $row['nilai_pengetahuan']; ?></td>
                                    <td style="padding: 0.85rem; font-weight: 700; color: var(--text-dark);"><?= $row['nilai_keterampilan']; ?></td>
                                    <td style="padding: 0.85rem;">
                                        <span class="badge badge-<?= strtolower($row['Keterangan']) === 'tuntas' ? 'success' : 'danger'; ?>" style="padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">
                                            <?= htmlspecialchars($row['Keterangan']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                    <i class="bi bi-folder2-open" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                    Belum ada data nilai rapor yang diinputkan oleh Guru pengampu.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>