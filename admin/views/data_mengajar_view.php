<?php
/**
 * View halaman data_mengajar.
 * Variabel disiapkan oleh controller data_mengajar.php.
 * File ini sengaja tidak memuat koneksi, otorisasi, atau layout global.
 */
?>
<div class="sipn-admin-data-page sipn-admin-data-page--mengajar">
    <section class="sipn-admin-data-hero sipn-admin-data-enter" style="--admin-enter-delay: 0ms;" aria-labelledby="mengajarPageTitle">
        <div class="sipn-admin-data-hero__copy">
            <span class="sipn-admin-data-eyebrow"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i> Manajemen Akademik</span>
            <h1 id="mengajarPageTitle">Data Penugasan Mengajar</h1>
            <p>Atur hubungan peserta didik dengan mata pelajaran agar penugasan akademik tercatat secara terstruktur.</p>
            <div class="sipn-admin-data-hero__meta">
                <span><i class="bi bi-link-45deg" aria-hidden="true"></i> Relasi siswa dan mata pelajaran</span>
                <span><i class="bi bi-shield-check" aria-hidden="true"></i> Panel administrator</span>
            </div>
        </div>
        <button type="button" class="sipn-admin-data-primary" data-modal-target="#modalTambah">
            <i class="bi bi-plus-circle-fill" aria-hidden="true"></i> Buat Penugasan
        </button>
        <i class="bi bi-diagram-3 sipn-admin-data-hero__ornament" aria-hidden="true"></i>
    </section>

    <?php if ($query_error): ?>
        <div class="sipn-admin-data-notice" role="status">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>Sebagian data penugasan atau pilihan formulir gagal dimuat. Periksa koneksi database dan tabel terkait.</span>
        </div>
    <?php endif; ?>

    <section class="sipn-admin-data-stats sipn-admin-data-enter" style="--admin-enter-delay: 70ms;" aria-label="Ringkasan penugasan">
        <article class="sipn-admin-data-stat">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--blue"><i class="bi bi-journal-check" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Total Penugasan</small><strong><?= number_format($total_penugasan, 0, ',', '.'); ?></strong><em>Relasi siswa dan mata pelajaran</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 115ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--violet"><i class="bi bi-book-half" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Mapel Terpakai</small><strong><?= number_format($jumlah_mapel_terpakai, 0, ',', '.'); ?></strong><em>Mata pelajaran memiliki peserta</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 160ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--green"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Siswa Terdaftar</small><strong><?= number_format($jumlah_siswa_terdaftar, 0, ',', '.'); ?></strong><em>Siswa dengan minimal satu penugasan</em></span>
        </article>
    </section>

    <section class="sipn-admin-data-panel sipn-admin-data-enter" style="--admin-enter-delay: 210ms;" aria-labelledby="mengajarTableTitle">
        <div class="sipn-admin-data-panel__heading">
            <div>
                <span class="sipn-admin-data-kicker">Direktori akademik</span>
                <h2 id="mengajarTableTitle">Daftar Penugasan Siswa</h2>
                <p>Telusuri mata pelajaran, guru pengampu, nama siswa, NIS, atau kelas.</p>
            </div>
            <span class="sipn-admin-data-total"><i class="bi bi-list-check" aria-hidden="true"></i> <?= number_format($total_penugasan, 0, ',', '.'); ?> penugasan</span>
        </div>

        <div class="sipn-admin-data-toolbar">
            <label class="sipn-admin-data-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" data-admin-table-search placeholder="Cari mapel, guru, siswa, NIS, atau kelas..." autocomplete="off" aria-label="Cari penugasan mengajar">
                <kbd>⌕</kbd>
            </label>
            <button type="button" class="sipn-admin-data-reset" data-admin-search-clear><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset</button>
            <span class="sipn-admin-data-result" aria-live="polite">Menampilkan <strong data-admin-results-count><?= $total_penugasan; ?></strong> dari <?= $total_penugasan; ?> penugasan</span>
        </div>

        <div class="sipn-admin-data-table-wrap">
            <table class="sipn-admin-data-table sipn-admin-data-table--mengajar" data-admin-search-table>
                <thead><tr>
                    <th class="sipn-admin-data-col-number">No.</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengampu</th>
                    <th>Peserta Didik</th>
                    <th>Kelas</th>
                    <th class="sipn-admin-data-col-action">Aksi</th>
                </tr></thead>
                <tbody>
                <?php if ($penugasan): ?>
                    <?php foreach ($penugasan as $index => $row): ?>
                    <tr data-admin-search-row style="--admin-row-delay: <?= min($index, 12) * 45; ?>ms;">
                        <td class="sipn-admin-data-number"><?= $index + 1; ?></td>
                        <td>
                            <div class="sipn-admin-data-subject">
                                <strong><?= $e($row['nama_mapel']); ?></strong>
                                <span class="sipn-admin-data-code"><?= $e($row['kode_mapel']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="sipn-admin-data-person sipn-admin-data-person--compact">
                                <span class="sipn-admin-data-avatar is-guru"><i class="bi bi-person-workspace" aria-hidden="true"></i></span>
                                <strong><?= $e($row['nama_guru']); ?></strong>
                            </div>
                        </td>
                        <td>
                            <div class="sipn-admin-data-person sipn-admin-data-person--compact">
                                <span class="sipn-admin-data-avatar is-siswa"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
                                <span class="sipn-admin-data-person__meta"><strong><?= $e($row['nama_siswa']); ?></strong><small><?= $e($row['nis']); ?></small></span>
                            </div>
                        </td>
                        <td><span class="sipn-admin-data-pill is-violet"><?= $e($row['kelas']); ?></span></td>
                        <td class="sipn-admin-data-actions" data-search-ignore>
                            <form method="POST" class="sipn-admin-inline-delete" onsubmit="return confirm('Hapus penugasan <?= $e($row['nama_siswa']); ?> pada mata pelajaran <?= $e($row['nama_mapel']); ?>?');">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id_mengajar" value="<?= (int) $row['id']; ?>">
                                <button type="submit" class="sipn-admin-icon-button is-delete" title="Hapus penugasan" aria-label="Hapus penugasan <?= $e($row['nama_siswa']); ?>"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="sipn-admin-data-empty"><i class="bi bi-diagram-3" aria-hidden="true"></i><strong>Belum ada penugasan</strong><span>Penugasan siswa ke mata pelajaran akan tampil di sini.</span></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="sipn-admin-data-no-match" data-admin-search-empty hidden><i class="bi bi-search" aria-hidden="true"></i><strong>Data tidak ditemukan</strong><span>Coba kata kunci lain atau tekan tombol reset.</span></div>
        </div>
    </section>
</div>

<div class="modal-overlay" id="modalTambah">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTambahMengajarTitle">
        <form method="POST">
            <div class="modal-header header-primary">
                <h5 id="modalTambahMengajarTitle">Buat Penugasan Siswa</h5>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-group">
                    <label class="form-label" for="mengajarMapel">Mata Pelajaran</label>
                    <select class="form-select" id="mengajarMapel" name="mapel_id" required>
                        <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapel_list as $mapel): ?>
                            <option value="<?= (int) $mapel['id']; ?>">[<?= $e($mapel['kode_mapel']); ?>] <?= $e($mapel['nama_mapel']); ?><?= !empty($mapel['nama_guru']) ? ' — ' . $e($mapel['nama_guru']) : ''; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="mengajarSiswa">Peserta Didik</label>
                    <select class="form-select" id="mengajarSiswa" name="siswa_id" required>
                        <option value="" disabled selected>-- Pilih Siswa --</option>
                        <?php foreach ($siswa_list as $siswa): ?>
                            <option value="<?= (int) $siswa['id']; ?>"><?= $e($siswa['nis']); ?> — <?= $e($siswa['nama']); ?> (<?= $e($siswa['kelas']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (!$mapel_list || !$siswa_list): ?>
                    <p class="sipn-admin-data-notice" style="margin-top: 1rem;"><i class="bi bi-info-circle" aria-hidden="true"></i> Tambahkan mata pelajaran dan data siswa terlebih dahulu sebelum membuat penugasan.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom" <?= (!$mapel_list || !$siswa_list) ? 'disabled' : ''; ?>><i class="bi bi-check2-circle" aria-hidden="true"></i> Simpan Penugasan</button>
            </div>
        </form>
    </div>
</div>

