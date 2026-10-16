<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['siswa']);

$user_id = $_SESSION['user_id'];

$query_siswa = mysqli_query($conn, "SELECT id, nama, nis, kelas FROM siswa WHERE user_id = '$user_id'");
$siswa = mysqli_fetch_assoc($query_siswa);
$id_siswa = $siswa['id'] ?? 0;

// Ambil data nilai rapor lengkap dari database[cite: 6]
$query_nilai = mysqli_query($conn, "
    SELECT 
        nr.nilai_kkm, 
        nr.nilai_pengetahuan, 
        nr.nilai_keterampilan, 
        nr.Keterangan, 
        m.kode_mapel, 
        m.nama_mapel, 
        g.nama AS nama_guru
    FROM nilai_rapor nr
    JOIN mapel m ON nr.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    WHERE nr.siswa_id = '$id_siswa'
    ORDER BY m.nama_mapel ASC
");

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="card-panel-clean print-area" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 2rem;">
    <!-- Header Rapor -->
    <div class="card-header-clean flex-between no-print" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-file-earmark-text text-primary"></i> Transkrip Laporan Hasil Belajar (Nilai Rapor)
        </h3>
        <button onclick="window.print()" class="btn btn-secondary btn-sm" style="background: var(--bg-surface); color: var(--text-dark); border: 1px solid var(--border-color); font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="bi bi-printer"></i> Cetak Dokumen Rapor
        </button>
    </div>
    
    <div class="card-body-clean" style="padding: 1.5rem;">
        
        <!-- Kop Informasi Resmi Sekolah (Hanya tampil saat cetak atau sebagai identitas halaman) -->
        <div class="print-only text-center mb-4" style="display: none; border-bottom: 2px solid #000; padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <h4 style="font-weight: 800; font-size: 1.2rem; text-transform: uppercase;">SMK SANGKURIANG 1 CIMAHI</h4>
            <p style="font-size: 0.85rem; margin-top: 2px;">Sistem Informasi Pengelolaan Nilai Terpadu (SIPN) T.A 2026/2027</p>
        </div>

        <!-- Informasi Identitas Siswa Card -->
        <div class="student-identity-box mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <table class="info-table" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 150px; color: var(--text-muted); font-size: 0.85rem; padding: 0.3rem 0;">Nama Peserta Didik</td>
                    <td style="width: 15px; color: var(--text-muted);">:</td>
                    <td style="font-weight: 700; color: var(--text-dark); font-size: 0.9rem;"><?= htmlspecialchars($siswa['nama']); ?></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-size: 0.85rem; padding: 0.3rem 0;">Nomor Induk Siswa (NIS)</td>
                    <td>:</td>
                    <td style="font-weight: 600; color: var(--text-dark); font-size: 0.9rem;"><?= htmlspecialchars($siswa['nis']); ?></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-size: 0.85rem; padding: 0.3rem 0;">Rombongan Belajar</td>
                    <td>:</td>
                    <td style="font-weight: 600; color: var(--text-dark); font-size: 0.9rem;"><?= htmlspecialchars($siswa['kelas']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Tabel Transkrip Nilai Rapor -->
        <div class="table-responsive">
            <table class="data-table bordered" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--bg-surface); border-bottom: 2px solid var(--border-color);">
                        <th rowspan="2" class="text-center" style="width: 50px; padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); vertical-align: middle;">No</th>
                        <th rowspan="2" style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); vertical-align: middle;">Mata Pelajaran</th>
                        <th rowspan="2" style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); vertical-align: middle;">Guru Pengampu</th>
                        <th rowspan="2" class="text-center" style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); vertical-align: middle;">KKM</th>
                        <th colspan="2" class="text-center" style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted);">Nilai Capaian Kompetensi</th>
                        <th rowspan="2" class="text-center" style="padding: 0.75rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); vertical-align: middle;">Keterangan</th>
                    </tr>
                    <tr style="background: var(--bg-surface); border-bottom: 2px solid var(--border-color);">
                        <th class="text-center" style="padding: 0.5rem; font-size: 0.75rem; color: var(--text-muted);">Pengetahuan</th>
                        <th class="text-center" style="padding: 0.5rem; font-size: 0.75rem; color: var(--text-muted);">Keterampilan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_nilai) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_nilai)): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td class="text-center" style="padding: 0.85rem; font-weight: 600; color: var(--text-muted);"><?= $no++; ?></td>
                                <td style="padding: 0.85rem;">
                                    <strong style="color: var(--text-dark);"><?= htmlspecialchars($row['nama_mapel']); ?></strong><br>
                                    <small style="color: var(--text-muted); font-family: monospace; font-size: 0.75rem;"><?= htmlspecialchars($row['kode_mapel']); ?></small>
                                </td>
                                <td style="padding: 0.85rem; color: var(--text-dark);"><?= htmlspecialchars($row['nama_guru']); ?></td>
                                <td class="text-center" style="padding: 0.85rem; font-weight: 600; color: var(--text-dark);"><?= $row['nilai_kkm']; ?></td>
                                <td class="text-center" style="padding: 0.85rem; font-weight: 700; color: var(--text-dark);"><?= $row['nilai_pengetahuan']; ?></td>
                                <td class="text-center" style="padding: 0.85rem; font-weight: 700; color: var(--text-dark);"><?= $row['nilai_keterampilan']; ?></td>
                                <td class="text-center" style="padding: 0.85rem;">
                                    <span class="badge badge-<?= strtolower($row['Keterangan']) === 'tuntas' ? 'success' : 'danger'; ?>" style="padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">
                                        <?= htmlspecialchars($row['Keterangan']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">
                                <i class="bi bi-journal-x" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                Belum ada data nilai rapor yang diinput oleh Guru[cite: 6].
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Cetak Khusus Mode Print -->
        <div class="print-signature print-only mt-5" style="margin-top: 3rem;">
            <div style="float: right; text-align: center; width: 250px;">
                <p style="font-size: 0.85rem; color: #000;">Cimahi, <?= date('d F Y'); ?><br>Mengetahui,<br>Wali Kelas / Guru Pengampu</p>
                <br><br><br>
                <p style="font-weight: bold; text-decoration: underline; color: #000;">( ........................................................... )</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print, header, footer, .app-header, .app-footer {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        .card-panel-clean {
            border: none !important;
            box-shadow: none !important;
            background: white !important;
        }
        body {
            background-color: white !important;
            color: black !important;
        }
        table {
            color: black !important;
        }
    }
    .print-only {
        display: none;
    }
</style>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>