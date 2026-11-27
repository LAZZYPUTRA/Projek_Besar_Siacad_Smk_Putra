<?php
/**
 * View siswa / lihat_nilai_rapor.
 * Variabel data disiapkan oleh controller lihat_nilai_rapor.php.
 * File ini hanya menangani presentasi; koneksi dan otorisasi tetap pada controller.
 */
?>
<div class="sipn-rapor-page">
    <section class="sipn-rapor-hero no-print" aria-labelledby="raporPageTitle">
        <div class="sipn-rapor-hero__copy">
            <span class="sipn-rapor-eyebrow">
                <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                Portal Akademik Peserta Didik
            </span>
            <h1 id="raporPageTitle">Nilai Rapor</h1>
            <p>Pantau hasil belajar dan capaian kompetensi dari seluruh mata pelajaran.</p>
        </div>
        <div class="sipn-rapor-hero__actions">
            <a class="sipn-rapor-button sipn-rapor-button--secondary" href="<?= BASE_URL; ?>siswa/dashboard_siswa.php">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <button type="button" data-print-trigger class="sipn-rapor-button sipn-rapor-button--primary">
                <i class="bi bi-printer" aria-hidden="true"></i>
                <span>Cetak Rapor</span>
            </button>
        </div>
    </section>

    <?php if (!$query_siswa || !$query_nilai): ?>
        <div class="sipn-rapor-alert" role="alert">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <div>
                <strong>Data rapor belum dapat dimuat.</strong>
                <span>Terjadi kendala saat mengambil data. Silakan muat ulang halaman atau hubungi administrator.</span>
            </div>
        </div>
    <?php elseif (!$siswa): ?>
        <div class="sipn-rapor-alert" role="alert">
            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
            <div>
                <strong>Identitas peserta didik tidak ditemukan.</strong>
                <span>Hubungi administrator untuk memeriksa keterhubungan akun dengan data siswa.</span>
            </div>
        </div>
    <?php endif; ?>

    <section class="sipn-rapor-identity" aria-label="Identitas peserta didik">
        <div class="sipn-rapor-identity__intro">
            <span class="sipn-rapor-identity__icon" aria-hidden="true"><i class="bi bi-person-badge-fill"></i></span>
            <div>
                <span class="sipn-rapor-section-kicker">Identitas Peserta Didik</span>
                <h2><?= htmlspecialchars((string) ($siswa['nama'] ?? 'Data siswa tidak tersedia'), ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Informasi akun yang terhubung dengan laporan hasil belajar.</p>
            </div>
        </div>
        <div class="sipn-rapor-identity__details">
            <div class="sipn-rapor-identity__item">
                <span>Nomor Induk Siswa</span>
                <strong><?= htmlspecialchars((string) ($siswa['nis'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="sipn-rapor-identity__item">
                <span>Rombongan Belajar</span>
                <strong><?= htmlspecialchars((string) ($siswa['kelas'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="sipn-rapor-identity__item sipn-rapor-identity__item--count">
                <span>Mata Pelajaran</span>
                <strong><?= number_format($total_mapel, 0, ',', '.'); ?> <small>mapel</small></strong>
            </div>
        </div>
    </section>

    <section class="sipn-rapor-card" aria-labelledby="raporTableTitle">
        <div class="sipn-rapor-card__header">
            <div>
                <span class="sipn-rapor-section-kicker">Rekapitulasi Akademik</span>
                <h2 id="raporTableTitle">Transkrip Nilai Rapor</h2>
                <p>Rincian nilai pengetahuan, keterampilan, dan status ketuntasan.</p>
            </div>
            <span class="sipn-rapor-total-pill">
                <i class="bi bi-journal-check" aria-hidden="true"></i>
                <?= number_format($total_mapel, 0, ',', '.'); ?> data nilai
            </span>
        </div>

        <div class="sipn-rapor-print-heading print-only">
            <h2>SMK SANGKURIANG 1 CIMAHI</h2>
            <p>Sistem Informasi Pengelolaan Nilai Terpadu (SIPN) — T.A. 2026/2027</p>
            <h3>TRANSKRIP LAPORAN HASIL BELAJAR</h3>
        </div>

        <?php if ($query_nilai && $total_mapel > 0): ?>
            <div class="sipn-rapor-table-wrap">
                <div class="table-responsive">
                    <table class="sipn-rapor-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="sipn-rapor-table__number">No.</th>
                                <th rowspan="2" class="sipn-rapor-table__subject">Mata Pelajaran</th>
                                <th rowspan="2" class="sipn-rapor-table__teacher">Guru Pengampu</th>
                                <th rowspan="2" class="sipn-rapor-table__center">KKM</th>
                                <th colspan="2" class="sipn-rapor-table__group">Capaian Kompetensi</th>
                                <th rowspan="2" class="sipn-rapor-table__center">Keterangan</th>
                            </tr>
                            <tr>
                                <th class="sipn-rapor-table__center">Pengetahuan</th>
                                <th class="sipn-rapor-table__center">Keterampilan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nomor = 1;
                            $urutan_baris = 0;
                            while ($row = mysqli_fetch_assoc($query_nilai)):
                                $keterangan = trim((string) ($row['Keterangan'] ?? '-'));
                                $is_tuntas = strtolower($keterangan) === 'tuntas';
                                $delay_baris = min($urutan_baris * 65, 780);
                            ?>
                                <tr class="sipn-rapor-row" style="--row-delay: <?= $delay_baris; ?>ms;">
                                    <td class="sipn-rapor-table__number"><?= $nomor++; ?></td>
                                    <td class="sipn-rapor-subject-cell">
                                        <strong><?= htmlspecialchars((string) $row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <span><?= htmlspecialchars((string) $row['kode_mapel'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td class="sipn-rapor-teacher-cell"><?= htmlspecialchars((string) $row['nama_guru'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="sipn-rapor-table__center"><span class="sipn-rapor-score sipn-rapor-score--target"><?= htmlspecialchars((string) $row['nilai_kkm'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td class="sipn-rapor-table__center"><span class="sipn-rapor-score"><?= htmlspecialchars((string) $row['nilai_pengetahuan'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td class="sipn-rapor-table__center"><span class="sipn-rapor-score"><?= htmlspecialchars((string) $row['nilai_keterampilan'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td class="sipn-rapor-table__center">
                                        <span class="sipn-rapor-status <?= $is_tuntas ? 'sipn-rapor-status--success' : 'sipn-rapor-status--other'; ?>">
                                            <i class="bi <?= $is_tuntas ? 'bi-check-circle-fill' : 'bi-info-circle-fill'; ?>" aria-hidden="true"></i>
                                            <?= htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php
                                $urutan_baris++;
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($query_nilai): ?>
            <div class="sipn-rapor-empty">
                <span class="sipn-rapor-empty__icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                <strong>Belum ada nilai rapor</strong>
                <p>Nilai akan ditampilkan di halaman ini setelah guru pengampu memasukkannya ke SIPN.</p>
            </div>
        <?php endif; ?>

        <div class="sipn-rapor-signature print-only">
            <p>Cimahi, <?= date('d F Y'); ?><br>Mengetahui,<br>Wali Kelas / Guru Pengampu</p>
            <span>( ........................................................... )</span>
        </div>
    </section>

    <p class="sipn-rapor-footnote no-print">
        <i class="bi bi-shield-check" aria-hidden="true"></i>
        Data nilai ditampilkan sesuai catatan yang tersimpan di sistem SIPN.
    </p>
</div>
