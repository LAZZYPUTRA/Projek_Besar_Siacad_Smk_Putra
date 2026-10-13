<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['siswa']);

$user_id = $_SESSION['user_id'];

$query_siswa = mysqli_query($conn, "SELECT id, nama, nis, kelas FROM siswa WHERE user_id = '$user_id'");
$siswa = mysqli_fetch_assoc($query_siswa);
$id_siswa = $siswa['id'] ?? 0;

// Ambil data nilai rapor lengkap dari database
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

<div class="card-panel-clean print-area">
    <div class="card-header-clean flex-between no-print">
        <h3><i class="bi bi-file-earmark-text"></i> Laporan Hasil Belajar (Nilai Rapor)</h3>
        <button onclick="window.print()" class="btn btn-secondary btn-sm">
            <i class="bi bi-printer"></i> Cetak Rapor
        </button>
    </div>
    
    <div class="card-body-clean">
        <!-- Informasi Identitas Siswa -->
        <div class="student-identity-box mb-4">
            <table class="info-table">
                <tr>
                    <td style="width: 140px;" class="text-muted">Nama Lengkap</td>
                    <td style="width: 10px;">:</td>
                    <td><strong><?= htmlspecialchars($siswa['nama']); ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">NIS</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($siswa['nis']); ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Kelas</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($siswa['kelas']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Tabel Transkrip Nilai Rapor -->
        <div class="table-responsive">
            <table class="data-table bordered">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center" style="width: 50px;">No</th>
                        <th rowspan="2">Mata Pelajaran</th>
                        <th rowspan="2">Guru Pengampu</th>
                        <th rowspan="2" class="text-center">KKM</th>
                        <th colspan="2" class="text-center">Nilai Capaian</th>
                        <th rowspan="2" class="text-center">Keterangan</th>
                    </tr>
                    <tr>
                        <th class="text-center">Pengetahuan</th>
                        <th class="text-center">Keterampilan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_nilai) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_nilai)): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                adapter:
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_mapel']); ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['kode_mapel']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                                <td class="text-center font-bold"><?= $row['nilai_kkm']; ?></td>
                                <td class="text-center"><?= $row['nilai_pengetahuan']; ?></td>
                                <td class="text-center"><?= $row['nilai_keterampilan']; ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= strtolower($row['Keterangan']) === 'tuntas' ? 'success' : 'danger'; ?>">
                                        <?= htmlspecialchars($row['Keterangan']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data nilai rapor yang diinput oleh Guru.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Cetak -->
        <div class="print-signature print-only mt-5">
            <div class="signature-column float-right text-right" style="float: right;">
                <p>Mengetahui,<br>Wali Kelas / Guru Pengampu</p>
                <br><br><br>
                <p class="font-bold underline">( ........................................................... )</p>
            </div>
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
        }
        body {
            background-color: white !important;
        }
    }
    .print-only {
        display: none;
    }
</style>

<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>