<?php
/**
 * View guru / dashboard_guru.
 * Variabel data disiapkan oleh controller dashboard_guru.php.
 * File ini hanya menangani presentasi; koneksi dan otorisasi tetap pada controller.
 */
?>
<div class="sipn-role-dashboard sipn-teacher-dashboard">
    <section class="sipn-role-hero" aria-labelledby="teacherDashboardTitle">
        <div class="sipn-role-hero__content">
            <span class="sipn-role-eyebrow">
                <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                Portal Akademik Pendidik
            </span>
            <h1 id="teacherDashboardTitle">Selamat Datang, <?= htmlspecialchars($nama_guru, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p>Pantau mata pelajaran dan kelola nilai peserta didik dari satu halaman.</p>
            <div class="sipn-role-identity">
                <span><i class="bi bi-person-vcard" aria-hidden="true"></i> NIP: <?= htmlspecialchars($nip, ENT_QUOTES, 'UTF-8'); ?></span>
                <span><i class="bi bi-person-badge" aria-hidden="true"></i> Akun Guru</span>
            </div>
        </div>
        <a class="sipn-role-hero__action" href="<?= BASE_URL; ?>guru/data_nilai_siswa.php">
            <i class="bi bi-pencil-square" aria-hidden="true"></i>
            <span>Kelola Nilai Siswa</span>
            <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
        </a>
    </section>

    <?php if ($dashboard_query_error): ?>
        <div class="sipn-role-notice" role="status">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>Sebagian data dashboard gagal dimuat. Periksa koneksi database atau struktur tabel terkait.</span>
        </div>
    <?php endif; ?>

    <section class="sipn-role-stats sipn-role-stats--three" aria-label="Ringkasan aktivitas guru">
        <article class="sipn-role-stat sipn-role-stat--blue">
            <span class="sipn-role-stat__label">Mata Pelajaran Diampu</span>
            <span class="sipn-role-stat__main">
                <strong class="sipn-role-stat__value"><?= $total_mapel !== null ? number_format($total_mapel, 0, ',', '.') : '—'; ?></strong>
                <span class="sipn-role-stat__icon" aria-hidden="true"><i class="bi bi-journal-bookmark-fill"></i></span>
            </span>
            <span class="sipn-role-stat__foot">Jumlah mata pelajaran yang terhubung dengan akun Anda</span>
        </article>
        <article class="sipn-role-stat sipn-role-stat--green">
            <span class="sipn-role-stat__label">Siswa Terdaftar di SIPN</span>
            <span class="sipn-role-stat__main">
                <strong class="sipn-role-stat__value"><?= $total_siswa !== null ? number_format($total_siswa, 0, ',', '.') : '—'; ?></strong>
                <span class="sipn-role-stat__icon" aria-hidden="true"><i class="bi bi-people-fill"></i></span>
            </span>
            <span class="sipn-role-stat__foot">Jumlah seluruh peserta didik dalam sistem</span>
        </article>
        <article class="sipn-role-stat sipn-role-stat--violet">
            <span class="sipn-role-stat__label">Nilai Rapor Terinput</span>
            <span class="sipn-role-stat__main">
                <strong class="sipn-role-stat__value"><?= $total_nilai_input !== null ? number_format($total_nilai_input, 0, ',', '.') : '—'; ?></strong>
                <span class="sipn-role-stat__icon" aria-hidden="true"><i class="bi bi-file-earmark-check-fill"></i></span>
            </span>
            <span class="sipn-role-stat__foot">Jumlah entri nilai pada mata pelajaran yang Anda ampu</span>
        </article>
    </section>

    <section class="sipn-role-panel sipn-role-shortcuts" aria-labelledby="teacherShortcutTitle">
        <div class="sipn-role-section-heading">
            <div>
                <span class="sipn-role-section-kicker">Navigasi cepat</span>
                <h2 id="teacherShortcutTitle">Ruang Kerja Guru</h2>
                <p>Akses fitur utama dan daftar mata pelajaran Anda.</p>
            </div>
            <span class="sipn-role-section-mark" aria-hidden="true"><i class="bi bi-grid-1x2-fill"></i></span>
        </div>
        <div class="sipn-role-quick-grid sipn-role-quick-grid--two">
            <a class="sipn-role-quick-link" href="<?= BASE_URL; ?>guru/data_nilai_siswa.php">
                <span class="sipn-role-quick-link__icon sipn-role-quick-link__icon--blue" aria-hidden="true"><i class="bi bi-pencil-square"></i></span>
                <span class="sipn-role-quick-link__text"><strong>Kelola Nilai Siswa</strong><small>Input dan perbarui nilai rapor</small></span>
                <i class="bi bi-chevron-right sipn-role-quick-link__arrow" aria-hidden="true"></i>
            </a>
            <a class="sipn-role-quick-link" href="#teacherSubjects">
                <span class="sipn-role-quick-link__icon sipn-role-quick-link__icon--green" aria-hidden="true"><i class="bi bi-journal-check"></i></span>
                <span class="sipn-role-quick-link__text"><strong>Mata Pelajaran Diampu</strong><small>Lihat daftar mata pelajaran Anda</small></span>
                <i class="bi bi-chevron-right sipn-role-quick-link__arrow" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <section class="sipn-role-panel sipn-role-data-panel" id="teacherSubjects" aria-labelledby="teacherSubjectsTitle">
        <div class="sipn-role-panel__heading">
            <div>
                <span class="sipn-role-section-kicker">Data akademik</span>
                <h2 id="teacherSubjectsTitle">Daftar Mata Pelajaran Diampu</h2>
                <p>Daftar mata pelajaran yang ditugaskan kepada akun guru ini.</p>
            </div>
            <span class="sipn-role-count-badge"><?= $total_mapel !== null ? number_format($total_mapel, 0, ',', '.') : '—'; ?> Mapel</span>
        </div>
        <div class="table-responsive">
            <table class="data-table sipn-role-table">
                <thead>
                    <tr>
                        <th style="width: 7%;">No</th>
                        <th style="width: 22%;">Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th class="text-center" style="width: 22%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_mapel_list && mysqli_num_rows($query_mapel_list) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_mapel_list)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><span class="sipn-role-code"><?= htmlspecialchars($row['kode_mapel'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><strong><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL; ?>guru/data_nilai_siswa.php" class="sipn-role-table-action">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i> Input Nilai
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="sipn-role-empty">
                                <i class="bi <?= $query_mapel_list ? 'bi-journal-x' : 'bi-exclamation-circle'; ?>" aria-hidden="true"></i>
                                <strong><?= $query_mapel_list ? 'Belum ada mata pelajaran' : 'Daftar mata pelajaran gagal dimuat'; ?></strong>
                                <span><?= $query_mapel_list ? 'Belum ada mata pelajaran yang ditugaskan kepada akun Anda.' : 'Silakan muat ulang halaman atau hubungi administrator.'; ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <p class="sipn-role-footnote"><i class="bi bi-info-circle" aria-hidden="true"></i> Ringkasan dihitung dari data yang tersimpan di database SIPN.</p>
</div>
