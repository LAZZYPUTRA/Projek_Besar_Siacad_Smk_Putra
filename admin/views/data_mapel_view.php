<?php
/**
 * View halaman data_mapel.
 * Variabel disiapkan oleh controller data_mapel.php.
 * File ini sengaja tidak memuat koneksi, otorisasi, atau layout global.
 */
?>
<div class="sipn-admin-data-page sipn-admin-data-page--mapel">
    <section class="sipn-admin-data-hero sipn-admin-data-enter" style="--admin-enter-delay: 0ms;" aria-labelledby="mapelPageTitle">
        <div class="sipn-admin-data-hero__copy">
            <span class="sipn-admin-data-eyebrow"><i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i> Manajemen Akademik</span>
            <h1 id="mapelPageTitle">Data Mata Pelajaran</h1>
            <p>Kelola katalog mata pelajaran, kode akademik, dan guru pengampu dalam satu tempat.</p>
            <div class="sipn-admin-data-hero__meta">
                <span><i class="bi bi-book-half" aria-hidden="true"></i> Kurikulum dan pembelajaran</span>
                <span><i class="bi bi-shield-check" aria-hidden="true"></i> Panel administrator</span>
            </div>
        </div>
        <button type="button" class="sipn-admin-data-primary" data-modal-target="#modalTambah" <?= !$guru_list ? 'disabled' : ''; ?>>
            <i class="bi bi-plus-circle-fill" aria-hidden="true"></i> Tambah Mata Pelajaran
        </button>
        <i class="bi bi-journal-bookmark sipn-admin-data-hero__ornament" aria-hidden="true"></i>
    </section>

    <?php if ($query_error): ?>
        <div class="sipn-admin-data-notice" role="status">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>Data mata pelajaran atau daftar guru pengampu gagal dimuat. Periksa koneksi database dan tabel terkait.</span>
        </div>
    <?php elseif (!$guru_list): ?>
        <div class="sipn-admin-data-notice" role="status">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <span>Belum ada data guru. Tambahkan data guru terlebih dahulu sebelum membuat mata pelajaran.</span>
        </div>
    <?php endif; ?>

    <section class="sipn-admin-data-stats sipn-admin-data-enter" style="--admin-enter-delay: 70ms;" aria-label="Ringkasan mata pelajaran">
        <article class="sipn-admin-data-stat">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--blue"><i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Total Mata Pelajaran</small><strong><?= number_format($total_mapel, 0, ',', '.'); ?></strong><em>Data mata pelajaran terdaftar</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 115ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--green"><i class="bi bi-person-workspace" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Guru Pengampu</small><strong><?= number_format($jumlah_guru_pengampu, 0, ',', '.'); ?></strong><em>Guru dengan penugasan mapel</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 160ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--violet"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Total Penugasan</small><strong><?= number_format($total_penugasan, 0, ',', '.'); ?></strong><em>Relasi siswa pada seluruh mapel</em></span>
        </article>
    </section>

    <section class="sipn-admin-data-panel sipn-admin-data-enter" style="--admin-enter-delay: 210ms;" aria-labelledby="mapelTableTitle">
        <div class="sipn-admin-data-panel__heading">
            <div>
                <span class="sipn-admin-data-kicker">Direktori pembelajaran</span>
                <h2 id="mapelTableTitle">Daftar Mata Pelajaran</h2>
                <p>Cari berdasarkan kode, nama mata pelajaran, atau guru pengampu.</p>
            </div>
            <span class="sipn-admin-data-total"><i class="bi bi-journal-text" aria-hidden="true"></i> <?= number_format($total_mapel, 0, ',', '.'); ?> mata pelajaran</span>
        </div>

        <div class="sipn-admin-data-toolbar">
            <label class="sipn-admin-data-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" data-admin-table-search placeholder="Cari kode, mata pelajaran, atau guru..." autocomplete="off" aria-label="Cari mata pelajaran">
                <kbd>⌕</kbd>
            </label>
            <button type="button" class="sipn-admin-data-reset" data-admin-search-clear><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset</button>
            <span class="sipn-admin-data-result" aria-live="polite">Menampilkan <strong data-admin-results-count><?= $total_mapel; ?></strong> dari <?= $total_mapel; ?> mata pelajaran</span>
        </div>

        <div class="sipn-admin-data-table-wrap">
            <table class="sipn-admin-data-table sipn-admin-data-table--mapel" data-admin-search-table>
                <thead><tr>
                    <th class="sipn-admin-data-col-number">No.</th>
                    <th>Kode</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengampu</th>
                    <th>Jumlah Siswa</th>
                    <th class="sipn-admin-data-col-action">Aksi</th>
                </tr></thead>
                <tbody>
                <?php if ($mapel_list): ?>
                    <?php foreach ($mapel_list as $index => $row): ?>
                    <?php $row_id = (int) $row['id']; ?>
                    <tr data-admin-search-row style="--admin-row-delay: <?= min($index, 12) * 45; ?>ms;">
                        <td class="sipn-admin-data-number"><?= $index + 1; ?></td>
                        <td><span class="sipn-admin-data-code"><?= $e($row['kode_mapel']); ?></span></td>
                        <td>
                            <div class="sipn-admin-data-person sipn-admin-data-person--compact">
                                <span class="sipn-admin-data-avatar is-mapel"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                                <strong><?= $e($row['nama_mapel']); ?></strong>
                            </div>
                        </td>
                        <td>
                            <div class="sipn-admin-data-person sipn-admin-data-person--compact">
                                <span class="sipn-admin-data-avatar is-guru"><i class="bi bi-person-workspace" aria-hidden="true"></i></span>
                                <strong><?= $e($row['nama_guru'] ?: 'Guru belum tersedia'); ?></strong>
                            </div>
                        </td>
                        <td><span class="sipn-admin-data-pill is-blue"><?= number_format((int) $row['total_siswa'], 0, ',', '.'); ?> siswa</span></td>
                        <td class="sipn-admin-data-actions" data-search-ignore>
                            <button type="button" class="sipn-admin-icon-button is-edit" data-modal-target="#modalEdit<?= $row_id; ?>" title="Edit mata pelajaran" aria-label="Edit <?= $e($row['nama_mapel']); ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                            <button type="button" class="sipn-admin-icon-button is-delete" data-modal-target="#modalHapus<?= $row_id; ?>" title="Hapus mata pelajaran" aria-label="Hapus <?= $e($row['nama_mapel']); ?>"><i class="bi bi-trash-fill" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="sipn-admin-data-empty"><i class="bi bi-journal-x" aria-hidden="true"></i><strong>Belum ada mata pelajaran</strong><span>Data yang ditambahkan akan muncul pada tabel ini.</span></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="sipn-admin-data-no-match" data-admin-search-empty hidden><i class="bi bi-search" aria-hidden="true"></i><strong>Data tidak ditemukan</strong><span>Coba kata kunci lain atau tekan tombol reset.</span></div>
        </div>
    </section>
</div>

<!-- Modal tambah, edit, dan hapus diletakkan di luar tabel. -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTambahMapelTitle">
        <form method="POST">
            <div class="modal-header header-primary">
                <h5 id="modalTambahMapelTitle">Tambah Mata Pelajaran</h5>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-group">
                    <label class="form-label" for="kodeMapelBaru">Kode Mata Pelajaran</label>
                    <input type="text" class="form-input" id="kodeMapelBaru" name="kode_mapel" maxlength="30" required placeholder="Contoh: MTK-01">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="namaMapelBaru">Nama Mata Pelajaran</label>
                    <input type="text" class="form-input" id="namaMapelBaru" name="nama_mapel" maxlength="150" required placeholder="Masukkan nama mata pelajaran">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="guruMapelBaru">Guru Pengampu</label>
                    <select class="form-select" id="guruMapelBaru" name="guru_id" required>
                        <option value="" disabled selected>-- Pilih Guru --</option>
                        <?php foreach ($guru_list as $guru): ?><option value="<?= (int) $guru['id']; ?>"><?= $e($guru['nama']); ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom" <?= !$guru_list ? 'disabled' : ''; ?>><i class="bi bi-check2-circle" aria-hidden="true"></i> Simpan Mata Pelajaran</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($mapel_list as $row): ?>
<?php $row_id = (int) $row['id']; ?>
<div class="modal-overlay" id="modalEdit<?= $row_id; ?>">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalEditMapelTitle<?= $row_id; ?>">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5 id="modalEditMapelTitle<?= $row_id; ?>">Edit Mata Pelajaran</h5>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id_mapel" value="<?= $row_id; ?>">
                <div class="form-group">
                    <label class="form-label" for="kodeMapel<?= $row_id; ?>">Kode Mata Pelajaran</label>
                    <input type="text" class="form-input" id="kodeMapel<?= $row_id; ?>" name="kode_mapel" maxlength="30" value="<?= $e($row['kode_mapel']); ?>" required>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="namaMapel<?= $row_id; ?>">Nama Mata Pelajaran</label>
                    <input type="text" class="form-input" id="namaMapel<?= $row_id; ?>" name="nama_mapel" maxlength="150" value="<?= $e($row['nama_mapel']); ?>" required>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="guruMapel<?= $row_id; ?>">Guru Pengampu</label>
                    <select class="form-select" id="guruMapel<?= $row_id; ?>" name="guru_id" required>
                        <?php foreach ($guru_list as $guru): ?>
                            <option value="<?= (int) $guru['id']; ?>" <?= (int) $row['guru_id'] === (int) $guru['id'] ? 'selected' : ''; ?>><?= $e($guru['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom"><i class="bi bi-check2-circle" aria-hidden="true"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalHapus<?= $row_id; ?>">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalHapusMapelTitle<?= $row_id; ?>">
        <form method="POST">
            <div class="modal-header header-danger">
                <h5 id="modalHapusMapelTitle<?= $row_id; ?>">Konfirmasi Hapus Mata Pelajaran</h5>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="id_mapel" value="<?= $row_id; ?>">
                <p>Hapus mata pelajaran <strong><?= $e($row['nama_mapel']); ?></strong> dengan kode <strong><?= $e($row['kode_mapel']); ?></strong>?</p>
                <p class="sipn-admin-user-warning"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> Jika mata pelajaran masih dipakai pada penugasan atau nilai rapor, database dapat menolak penghapusan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash-fill" aria-hidden="true"></i> Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

