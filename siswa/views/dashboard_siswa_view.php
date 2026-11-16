<?php
/**
 * View siswa / dashboard_siswa.
 * Variabel data disiapkan oleh controller dashboard_siswa.php.
 * File ini hanya menangani presentasi; koneksi dan otorisasi tetap pada controller.
 */
?>
<div class="sipn-role-dashboard sipn-student-dashboard">
    <section class="sipn-role-hero" aria-labelledby="studentDashboardTitle">
        <div class="sipn-role-hero__content">
            <span class="sipn-role-eyebrow">
                <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                Portal Akademik Peserta Didik
            </span>
            <h1 id="studentDashboardTitle">Selamat Datang, <?= htmlspecialchars($nama_siswa, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p>Pantau ringkasan mata pelajaran dan perkembangan nilai rapor Anda.</p>
            <div class="sipn-role-identity">
                <span><i class="bi bi-card-text" aria-hidden="true"></i> NIS: <?= htmlspecialchars($nis, ENT_QUOTES, 'UTF-8'); ?></span>
                <span><i class="bi bi-people" aria-hidden="true"></i> Kelas: <?= htmlspecialchars($kelas, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
        <a class="sipn-role-hero__action" href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php">
            <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
            <span>Buka Transkrip Lengkap</span>
            <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
        </a>
    </section>

    <?php if ($dashboard_query_error): ?>
        <div class="sipn-role-notice" role="status">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>Sebagian data akademik gagal dimuat. Periksa koneksi database atau hubungi administrator.</span>
        </div>
    <?php endif; ?>

    <section class="sipn-role-stats sipn-role-stats--two" aria-label="Ringkasan akademik siswa">
        <article class="sipn-role-stat sipn-role-stat--blue">
            <span class="sipn-role-stat__label">Mata Pelajaran Diikuti</span>
            <span class="sipn-role-stat__main">
                <strong class="sipn-role-stat__value"><?= $total_mapel !== null ? number_format($total_mapel, 0, ',', '.') : '—'; ?></strong>
                <span class="sipn-role-stat__icon" aria-hidden="true"><i class="bi bi-journal-bookmark-fill"></i></span>
            </span>
            <span class="sipn-role-stat__foot">Jumlah penugasan mata pelajaran pada akun Anda</span>
        </article>
        <article class="sipn-role-stat sipn-role-stat--green">
            <span class="sipn-role-stat__label">Nilai Rapor Tercatat</span>
            <span class="sipn-role-stat__main">
                <strong class="sipn-role-stat__value"><?= $total_nilai !== null ? number_format($total_nilai, 0, ',', '.') : '—'; ?></strong>
                <span class="sipn-role-stat__icon" aria-hidden="true"><i class="bi bi-award-fill"></i></span>
            </span>
            <span class="sipn-role-stat__foot">Jumlah entri nilai rapor yang telah tersedia</span>
        </article>
    </section>

    <section class="sipn-role-panel sipn-role-shortcuts" aria-labelledby="studentShortcutTitle">
        <div class="sipn-role-section-heading">
            <div>
                <span class="sipn-role-section-kicker">Navigasi cepat</span>
                <h2 id="studentShortcutTitle">Akses Akademik</h2>
                <p>Buka transkrip atau lihat ringkasan nilai pada halaman ini.</p>
            </div>
            <span class="sipn-role-section-mark" aria-hidden="true"><i class="bi bi-book-half"></i></span>
        </div>
        <div class="sipn-role-quick-grid sipn-role-quick-grid--two">
            <a class="sipn-role-quick-link" href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php">
                <span class="sipn-role-quick-link__icon sipn-role-quick-link__icon--blue" aria-hidden="true"><i class="bi bi-file-earmark-bar-graph"></i></span>
                <span class="sipn-role-quick-link__text"><strong>Lihat Nilai Rapor</strong><small>Buka transkrip akademik lengkap</small></span>
                <i class="bi bi-chevron-right sipn-role-quick-link__arrow" aria-hidden="true"></i>
            </a>
            <a class="sipn-role-quick-link" href="#studentGrades">
                <span class="sipn-role-quick-link__icon sipn-role-quick-link__icon--green" aria-hidden="true"><i class="bi bi-table"></i></span>
                <span class="sipn-role-quick-link__text"><strong>Ringkasan Nilai</strong><small>Lihat data nilai yang tercatat</small></span>
                <i class="bi bi-chevron-right sipn-role-quick-link__arrow" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <section class="sipn-role-panel sipn-role-data-panel" id="studentGrades" aria-labelledby="studentGradesTitle">
        <div class="sipn-role-panel__heading">
            <div>
                <span class="sipn-role-section-kicker">Data akademik</span>
                <h2 id="studentGradesTitle">Ringkasan Nilai Capaian Kompetensi Rapor</h2>
                <p>Nilai yang telah dimasukkan oleh guru pengampu.</p>
            </div>
            <a class="sipn-role-heading-link" href="<?= BASE_URL; ?>siswa/lihat_nilai_rapor.php">Lihat selengkapnya <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </div>
        <div class="table-responsive">
            <table class="data-table sipn-role-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th>Mata Pelajaran</th>
                        <th>KKM</th>
                        <th>Pengetahuan</th>
                        <th>Keterampilan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_nilai_list && mysqli_num_rows($query_nilai_list) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_nilai_list)): ?>
                            <?php $status_tuntas = strtolower(trim((string) ($row['Keterangan'] ?? ''))) === 'tuntas'; ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small class="sipn-role-table__subtext"><?= htmlspecialchars($row['kode_mapel'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                <td><?= htmlspecialchars((string) $row['nilai_kkm'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><strong><?= htmlspecialchars((string) $row['nilai_pengetahuan'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td><strong><?= htmlspecialchars((string) $row['nilai_keterampilan'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td>
                                    <span class="sipn-role-status <?= $status_tuntas ? 'sipn-role-status--success' : 'sipn-role-status--warning'; ?>">
                                        <?= htmlspecialchars((string) ($row['Keterangan'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="sipn-role-empty">
                                <i class="bi <?= $query_nilai_list ? 'bi-folder2-open' : 'bi-exclamation-circle'; ?>" aria-hidden="true"></i>
                                <strong><?= $query_nilai_list ? 'Belum ada nilai rapor' : 'Daftar nilai gagal dimuat'; ?></strong>
                                <span><?= $query_nilai_list ? 'Nilai akan tampil di sini setelah guru pengampu memasukkannya.' : 'Silakan muat ulang halaman atau hubungi administrator.'; ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <p class="sipn-role-footnote"><i class="bi bi-info-circle" aria-hidden="true"></i> Ringkasan ditampilkan berdasarkan data nilai yang tersimpan di database SIPN.</p>
</div>
