<?php
/**
 * View guru / data_nilai_siswa.
 * Variabel data disiapkan oleh controller data_nilai_siswa.php.
 * File ini hanya menangani presentasi; koneksi dan otorisasi tetap pada controller.
 */
?>
<div class="sipn-grade-page" id="sipnGradePage">
    <section class="sipn-grade-hero sipn-grade-enter" aria-labelledby="gradePageTitle">
        <div class="sipn-grade-hero__content">
            <span class="sipn-grade-eyebrow">
                <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                Portal Akademik Pendidik
            </span>
            <h1 id="gradePageTitle">Kelola Nilai Siswa</h1>
            <p>Kelola nilai pengetahuan, keterampilan, dan ketuntasan belajar siswa pada mata pelajaran yang Anda ampu.</p>
            <div class="sipn-grade-hero__meta">
                <span><i class="bi bi-person-workspace" aria-hidden="true"></i><?= $escape($nama_guru); ?></span>
                <span><i class="bi bi-shield-check" aria-hidden="true"></i>Akses guru terverifikasi</span>
            </div>
        </div>
        <button type="button" class="sipn-grade-primary-action" data-modal-target="#modalTambahNilai" <?= empty($mapel_list) ? 'disabled' : ''; ?>>
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <span>Input Nilai Baru</span>
        </button>
        <span class="sipn-grade-hero__decoration" aria-hidden="true"></span>
    </section>

    <?php if ($guru_error): ?>
        <div class="sipn-grade-notice sipn-grade-notice--danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>Profil guru tidak ditemukan pada akun ini. Hubungi administrator sekolah agar data akun diperiksa.</span>
        </div>
    <?php endif; ?>

    <section class="sipn-grade-stats" aria-label="Ringkasan data nilai">
        <article class="sipn-grade-stat sipn-grade-enter" style="--grade-enter-delay: 70ms;">
            <span class="sipn-grade-stat__icon sipn-grade-stat__icon--blue"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
            <span class="sipn-grade-stat__label">Total Entri Nilai</span>
            <strong class="sipn-grade-stat__value"><?= number_format($total_nilai, 0, ',', '.'); ?></strong>
            <span class="sipn-grade-stat__foot">Data nilai mata pelajaran Anda</span>
        </article>
        <article class="sipn-grade-stat sipn-grade-enter" style="--grade-enter-delay: 130ms;">
            <span class="sipn-grade-stat__icon sipn-grade-stat__icon--green"><i class="bi bi-check-circle-fill" aria-hidden="true"></i></span>
            <span class="sipn-grade-stat__label">Siswa Tuntas</span>
            <strong class="sipn-grade-stat__value"><?= number_format($total_tuntas, 0, ',', '.'); ?></strong>
            <span class="sipn-grade-stat__foot">Berdasarkan status yang tersimpan</span>
        </article>
        <article class="sipn-grade-stat sipn-grade-enter" style="--grade-enter-delay: 190ms;">
            <span class="sipn-grade-stat__icon sipn-grade-stat__icon--orange"><i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i></span>
            <span class="sipn-grade-stat__label">Belum Tuntas</span>
            <strong class="sipn-grade-stat__value"><?= number_format($total_belum_tuntas, 0, ',', '.'); ?></strong>
            <span class="sipn-grade-stat__foot">Perlu pemantauan hasil belajar</span>
        </article>
        <article class="sipn-grade-stat sipn-grade-enter" style="--grade-enter-delay: 250ms;">
            <span class="sipn-grade-stat__icon sipn-grade-stat__icon--violet"><i class="bi bi-book-half" aria-hidden="true"></i></span>
            <span class="sipn-grade-stat__label">Mata Pelajaran</span>
            <strong class="sipn-grade-stat__value"><?= number_format(count($mapel_list), 0, ',', '.'); ?></strong>
            <span class="sipn-grade-stat__foot">Mata pelajaran yang diampu</span>
        </article>
    </section>

    <section class="sipn-grade-panel sipn-grade-enter" style="--grade-enter-delay: 300ms;" aria-labelledby="gradeTableTitle">
        <div class="sipn-grade-panel__heading">
            <div>
                <span class="sipn-grade-section-kicker">Data akademik</span>
                <h2 id="gradeTableTitle">Daftar Nilai Rapor Siswa</h2>
                <p>Data yang ditampilkan hanya berasal dari mata pelajaran yang ditugaskan kepada Anda.</p>
            </div>
            <span class="sipn-grade-total-badge"><i class="bi bi-database-check" aria-hidden="true"></i><?= number_format($total_nilai, 0, ',', '.'); ?> entri</span>
        </div>

        <div class="sipn-grade-toolbar">
            <label class="sipn-grade-search" for="gradeSearch">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" id="gradeSearch" placeholder="Cari nama, NIS, kelas, atau mapel..." autocomplete="off">
            </label>
            <label class="sipn-grade-filter">
                <span class="sr-only">Filter status nilai</span>
                <select id="gradeStatusFilter">
                    <option value="">Semua status</option>
                    <option value="tuntas">Tuntas</option>
                    <option value="belum tuntas">Belum tuntas</option>
                </select>
            </label>
            <label class="sipn-grade-filter">
                <span class="sr-only">Filter mata pelajaran</span>
                <select id="gradeSubjectFilter">
                    <option value="">Semua mata pelajaran</option>
                    <?php foreach ($mapel_list as $mapel): ?>
                        <option value="<?= (int) $mapel['id']; ?>"><?= $escape($mapel['nama_mapel']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="button" class="sipn-grade-reset" id="gradeFilterReset">
                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                Reset
            </button>
        </div>

        <div class="sipn-grade-table-meta">
            <span id="gradeResultCount" aria-live="polite">Menampilkan <?= number_format($total_nilai, 0, ',', '.'); ?> data</span>
            <span><i class="bi bi-info-circle" aria-hidden="true"></i> Nilai rata-rata = (pengetahuan + keterampilan) ÷ 2</span>
        </div>

        <div class="sipn-grade-table-wrap">
            <table class="sipn-grade-table" id="gradeTable">
                <thead>
                    <tr>
                        <th scope="col" class="sipn-grade-col-number">No.</th>
                        <th scope="col">Identitas Siswa</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Mata Pelajaran</th>
                        <th scope="col" class="is-center">KKM</th>
                        <th scope="col" class="is-center">Pengetahuan</th>
                        <th scope="col" class="is-center">Keterampilan</th>
                        <th scope="col" class="is-center">Rata-rata</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="is-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_nilai > 0): ?>
                        <?php foreach ($nilai_list as $index => $row): ?>
                            <?php
                                $nilai_pengetahuan = (int) $row['nilai_pengetahuan'];
                                $nilai_keterampilan = (int) $row['nilai_keterampilan'];
                                $rata_rata_row = ($nilai_pengetahuan + $nilai_keterampilan) / 2;
                                $status_normal = strtolower(trim((string) $row['Keterangan']));
                                $status_class = $status_normal === 'tuntas'
                                    ? 'sipn-grade-status--success'
                                    : ($status_normal === 'belum tuntas' ? 'sipn-grade-status--warning' : 'sipn-grade-status--neutral');
                                $status_label = trim((string) $row['Keterangan']) !== '' ? $row['Keterangan'] : 'Belum ditentukan';
                                $search_text = implode(' ', [
                                    $row['nis'],
                                    $row['nama_siswa'],
                                    $row['kelas'],
                                    $row['nama_mapel'],
                                    $row['kode_mapel']
                                ]);
                            ?>
                            <tr
                                data-grade-row
                                data-search="<?= $escape($search_text); ?>"
                                data-status="<?= $escape($status_normal); ?>"
                                data-mapel="<?= (int) $row['mapel_id']; ?>"
                                style="--grade-row-delay: <?= min($index * 55, 660); ?>ms;"
                            >
                                <td class="sipn-grade-col-number"><span class="sipn-grade-row-number"><?= number_format($index + 1, 0, ',', '.'); ?></span></td>
                                <td>
                                    <div class="sipn-grade-student">
                                        <span class="sipn-grade-student__avatar" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
                                        <span class="sipn-grade-student__details">
                                            <strong><?= $escape($row['nama_siswa']); ?></strong>
                                            <small>NIS <?= $escape($row['nis']); ?></small>
                                        </span>
                                    </div>
                                </td>
                                <td><span class="sipn-grade-class"><?= $escape($row['kelas']); ?></span></td>
                                <td>
                                    <span class="sipn-grade-subject">
                                        <strong><?= $escape($row['nama_mapel']); ?></strong>
                                        <small><?= $escape($row['kode_mapel']); ?></small>
                                    </span>
                                </td>
                                <td class="is-center"><span class="sipn-grade-score sipn-grade-score--muted"><?= (int) $row['nilai_kkm']; ?></span></td>
                                <td class="is-center"><strong class="sipn-grade-score-value"><?= $nilai_pengetahuan; ?></strong></td>
                                <td class="is-center"><strong class="sipn-grade-score-value"><?= $nilai_keterampilan; ?></strong></td>
                                <td class="is-center"><span class="sipn-grade-average"><?= number_format($rata_rata_row, 1, ',', '.'); ?></span></td>
                                <td><span class="sipn-grade-status <?= $status_class; ?>"><i class="bi <?= $status_normal === 'tuntas' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'; ?>" aria-hidden="true"></i><?= $escape($status_label); ?></span></td>
                                <td class="is-center">
                                    <div class="sipn-grade-actions">
                                        <button type="button" class="sipn-grade-icon-button sipn-grade-icon-button--edit" data-modal-target="#modalEditNilai<?= (int) $row['id']; ?>" aria-label="Edit nilai <?= $escape($row['nama_siswa']); ?>" title="Edit nilai">
                                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="sipn-grade-icon-button sipn-grade-icon-button--delete" data-modal-target="#modalHapusNilai<?= (int) $row['id']; ?>" aria-label="Hapus nilai <?= $escape($row['nama_siswa']); ?>" title="Hapus nilai">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="gradeNoResults" hidden>
                            <td colspan="10">
                                <div class="sipn-grade-no-results">
                                    <i class="bi bi-search" aria-hidden="true"></i>
                                    <strong>Data tidak ditemukan</strong>
                                    <span>Coba ubah kata kunci atau filter yang digunakan.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">
                                <div class="sipn-grade-empty">
                                    <span class="sipn-grade-empty__icon"><i class="bi bi-journal-x" aria-hidden="true"></i></span>
                                    <strong>Belum ada nilai rapor</strong>
                                    <p>Belum ada nilai yang tercatat untuk mata pelajaran yang Anda ampu. Gunakan tombol “Input Nilai Baru” untuk mulai mengisi nilai.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="sipn-grade-panel__footer">
            <span><i class="bi bi-shield-check" aria-hidden="true"></i> Data dibatasi berdasarkan mata pelajaran milik akun guru aktif.</span>
            <span>Terakhir dimuat: <?= date('d/m/Y H:i'); ?></span>
        </div>
    </section>
</div>

<!-- Modal input nilai baru -->
<div class="modal-overlay sipn-grade-modal" id="modalTambahNilai" aria-hidden="true">
    <div class="modal-box modal-lg">
        <form method="POST" autocomplete="off">
            <div class="modal-header header-primary">
                <div>
                    <span class="sipn-grade-modal-kicker">Entri akademik</span>
                    <h5>Input Nilai Rapor Siswa</h5>
                </div>
                <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token); ?>">

                <div class="sipn-grade-form-section">
                    <span class="sipn-grade-form-section__title"><i class="bi bi-person-vcard" aria-hidden="true"></i> Data peserta didik</span>
                    <div class="sipn-grade-form-grid">
                        <div class="form-group">
                            <label class="form-label" for="gradeAddSubject">Mata Pelajaran</label>
                            <select name="mapel_id" id="gradeAddSubject" class="form-control" required>
                                <option value="" disabled selected>-- Pilih mata pelajaran --</option>
                                <?php foreach ($mapel_list as $mapel): ?>
                                    <option value="<?= (int) $mapel['id']; ?>">[<?= $escape($mapel['kode_mapel']); ?>] <?= $escape($mapel['nama_mapel']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="gradeAddStudent">Nama Siswa</label>
                            <select name="siswa_id" id="gradeAddStudent" class="form-control" required>
                                <option value="" disabled selected>-- Pilih siswa --</option>
                                <?php foreach ($siswa_list as $siswa): ?>
                                    <option value="<?= (int) $siswa['id']; ?>"><?= $escape($siswa['nis']); ?> — <?= $escape($siswa['nama']); ?> (<?= $escape($siswa['kelas']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="sipn-grade-form-section">
                    <span class="sipn-grade-form-section__title"><i class="bi bi-clipboard-data" aria-hidden="true"></i> Komponen penilaian</span>
                    <div class="sipn-grade-form-grid sipn-grade-form-grid--three">
                        <div class="form-group">
                            <label class="form-label" for="gradeAddKKM">Batas KKM</label>
                            <input type="number" min="0" max="100" name="nilai_kkm" id="gradeAddKKM" class="form-control" required placeholder="75">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="gradeAddKnowledge">Pengetahuan</label>
                            <input type="number" min="0" max="100" name="nilai_pengetahuan" id="gradeAddKnowledge" class="form-control" required placeholder="0–100">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="gradeAddSkill">Keterampilan</label>
                            <input type="number" min="0" max="100" name="nilai_keterampilan" id="gradeAddSkill" class="form-control" required placeholder="0–100">
                        </div>
                    </div>
                    <p class="sipn-grade-form-hint"><i class="bi bi-info-circle" aria-hidden="true"></i> Status ketuntasan dihitung otomatis dari rata-rata nilai pengetahuan dan keterampilan dibandingkan dengan KKM.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                <button type="submit" name="simpan_nilai" class="btn-primary-custom"><i class="bi bi-save" aria-hidden="true"></i> Simpan Nilai</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal edit dan hapus -->
<?php foreach ($nilai_list as $row): ?>
    <div class="modal-overlay sipn-grade-modal" id="modalEditNilai<?= (int) $row['id']; ?>" aria-hidden="true">
        <div class="modal-box modal-lg">
            <form method="POST" autocomplete="off">
                <div class="modal-header header-warning">
                    <div>
                        <span class="sipn-grade-modal-kicker">Perbarui entri akademik</span>
                        <h5>Edit Nilai Rapor</h5>
                    </div>
                    <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token); ?>">
                    <input type="hidden" name="mapel_id" value="<?= (int) $row['mapel_id']; ?>">
                    <input type="hidden" name="siswa_id" value="<?= (int) $row['siswa_id']; ?>">

                    <div class="sipn-grade-record-preview">
                        <span><i class="bi bi-person-fill" aria-hidden="true"></i></span>
                        <div>
                            <strong><?= $escape($row['nama_siswa']); ?></strong>
                            <small>NIS <?= $escape($row['nis']); ?> · <?= $escape($row['kelas']); ?> · <?= $escape($row['nama_mapel']); ?></small>
                        </div>
                    </div>

                    <div class="sipn-grade-form-section">
                        <span class="sipn-grade-form-section__title"><i class="bi bi-clipboard-data" aria-hidden="true"></i> Komponen penilaian</span>
                        <div class="sipn-grade-form-grid sipn-grade-form-grid--three">
                            <div class="form-group">
                                <label class="form-label" for="editKKM<?= (int) $row['id']; ?>">Batas KKM</label>
                                <input type="number" min="0" max="100" name="nilai_kkm" id="editKKM<?= (int) $row['id']; ?>" class="form-control" value="<?= (int) $row['nilai_kkm']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="editKnowledge<?= (int) $row['id']; ?>">Pengetahuan</label>
                                <input type="number" min="0" max="100" name="nilai_pengetahuan" id="editKnowledge<?= (int) $row['id']; ?>" class="form-control" value="<?= (int) $row['nilai_pengetahuan']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="editSkill<?= (int) $row['id']; ?>">Keterampilan</label>
                                <input type="number" min="0" max="100" name="nilai_keterampilan" id="editSkill<?= (int) $row['id']; ?>" class="form-control" value="<?= (int) $row['nilai_keterampilan']; ?>" required>
                            </div>
                        </div>
                        <p class="sipn-grade-form-hint"><i class="bi bi-info-circle" aria-hidden="true"></i> Perubahan akan langsung memperbarui entri nilai siswa ini.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" name="simpan_nilai" class="btn btn-warning"><i class="bi bi-save" aria-hidden="true"></i> Perbarui Nilai</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay sipn-grade-modal" id="modalHapusNilai<?= (int) $row['id']; ?>" aria-hidden="true">
        <div class="modal-box">
            <form method="POST">
                <div class="modal-header header-danger">
                    <div>
                        <span class="sipn-grade-modal-kicker">Konfirmasi tindakan</span>
                        <h5>Hapus Nilai Rapor</h5>
                    </div>
                    <button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token); ?>">
                    <input type="hidden" name="aksi" value="hapus_nilai">
                    <input type="hidden" name="id_nilai" value="<?= (int) $row['id']; ?>">
                    <div class="sipn-grade-delete-preview">
                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                        <p>Hapus nilai untuk <strong><?= $escape($row['nama_siswa']); ?></strong> pada mata pelajaran <strong><?= $escape($row['nama_mapel']); ?></strong>?</p>
                    </div>
                    <p class="sipn-grade-delete-note">Data nilai yang telah dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3" aria-hidden="true"></i> Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>


