<?php
/**
 * View halaman data_guru.
 * Variabel disiapkan oleh controller data_guru.php.
 * File ini sengaja tidak memuat koneksi, otorisasi, atau layout global.
 */
?>
<div class="sipn-admin-data-page sipn-admin-data-page--guru">
    <header class="sipn-admin-data-hero sipn-admin-data-enter" style="--admin-enter-delay: 0ms;">
        <div class="sipn-admin-data-hero__copy">
            <span class="sipn-admin-data-eyebrow"><i class="bi bi-shield-check" aria-hidden="true"></i> Administrasi Pendidik</span>
            <h1>Manajemen Data Guru</h1>
            <p>Kelola identitas pendidik dan akun akses portal SIPN dalam satu tempat.</p>
            <div class="sipn-admin-data-hero__meta">
                <span><i class="bi bi-people-fill" aria-hidden="true"></i> <?= number_format($total_guru, 0, ',', '.'); ?> guru terdaftar</span>
                <span><i class="bi bi-database-check" aria-hidden="true"></i> Data terhubung dengan akun SIPN</span>
            </div>
        </div>
        <button type="button" class="sipn-admin-data-primary" data-modal-target="#modalTambah">
            <i class="bi bi-person-plus-fill" aria-hidden="true"></i> Tambah Guru Baru
        </button>
        <span class="sipn-admin-data-hero__ornament" aria-hidden="true"><i class="bi bi-mortarboard-fill"></i></span>
    </header>

    <?php if ($data_guru_error): ?>
        <div class="sipn-admin-data-notice" role="alert"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> Data guru gagal dimuat. Periksa koneksi database, lalu muat ulang halaman.</div>
    <?php endif; ?>

    <section class="sipn-admin-data-stats sipn-admin-data-stats--three" aria-label="Ringkasan data guru">
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 70ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--blue"><i class="bi bi-person-workspace" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Total Guru</small><strong><?= number_format($total_guru, 0, ',', '.'); ?></strong><em>Seluruh pendidik terdaftar</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 125ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--green"><i class="bi bi-gender-male" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Laki-laki</small><strong><?= number_format($total_guru_laki, 0, ',', '.'); ?></strong><em>Data berdasarkan jenis kelamin</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 180ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--violet"><i class="bi bi-gender-female" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Perempuan</small><strong><?= number_format($total_guru_perempuan, 0, ',', '.'); ?></strong><em>Data berdasarkan jenis kelamin</em></span>
        </article>
    </section>

    <section class="sipn-admin-data-panel sipn-admin-data-enter" style="--admin-enter-delay: 225ms;" aria-labelledby="teacherTableTitle">
        <div class="sipn-admin-data-panel__heading">
            <div>
                <span class="sipn-admin-data-kicker">Direktori pendidik</span>
                <h2 id="teacherTableTitle">Daftar Guru</h2>
                <p>Gunakan pencarian untuk menemukan guru berdasarkan NIP, nama, jenis kelamin, atau username.</p>
            </div>
            <span class="sipn-admin-data-total"><i class="bi bi-person-lines-fill" aria-hidden="true"></i> <?= number_format($total_guru, 0, ',', '.'); ?> data</span>
        </div>

        <div class="sipn-admin-data-toolbar">
            <label class="sipn-admin-data-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" data-admin-table-search placeholder="Cari NIP, nama guru, atau username..." autocomplete="off" aria-label="Cari data guru">
                <kbd>⌕</kbd>
            </label>
            <button type="button" class="sipn-admin-data-reset" data-admin-search-clear><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset</button>
            <span class="sipn-admin-data-result" aria-live="polite">Menampilkan <strong data-admin-results-count><?= $total_guru; ?></strong> dari <?= $total_guru; ?> guru</span>
        </div>

        <div class="sipn-admin-data-table-wrap">
            <table class="sipn-admin-data-table" data-admin-search-table>
                <thead><tr>
                    <th class="sipn-admin-data-col-number">No.</th>
                    <th>NIP</th>
                    <th>Nama Guru &amp; Gelar</th>
                    <th>Jenis Kelamin</th>
                    <th>Username Akun</th>
                    <th class="sipn-admin-data-col-action">Aksi</th>
                </tr></thead>
                <tbody>
                <?php if ($teachers): ?>
                    <?php foreach ($teachers as $index => $row): ?>
                    <tr data-admin-search-row style="--admin-row-delay: <?= min($index, 12) * 45; ?>ms;">
                        <td class="sipn-admin-data-number"><?= $index + 1; ?></td>
                        <td><span class="sipn-admin-data-code"><?= htmlspecialchars((string) $row['nip'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><div class="sipn-admin-data-person"><span class="sipn-admin-data-avatar"><i class="bi bi-person-fill" aria-hidden="true"></i></span><strong><?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?></strong></div></td>
                        <td><span class="sipn-admin-data-pill <?= ($row['jenis_kelamin'] ?? '') === 'L' ? 'is-blue' : 'is-violet'; ?>"><?= ($row['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan'; ?></span></td>
                        <td><span class="sipn-admin-data-username"><i class="bi bi-person-circle" aria-hidden="true"></i><?= htmlspecialchars((string) $row['username'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="sipn-admin-data-actions" data-search-ignore>
                            <button type="button" class="sipn-admin-icon-button is-edit" data-modal-target="#modalEdit<?= (int) $row['id']; ?>" title="Edit data guru" aria-label="Edit <?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                            <button type="button" class="sipn-admin-icon-button is-delete" data-modal-target="#modalHapus<?= (int) $row['id']; ?>" title="Hapus data guru" aria-label="Hapus <?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="sipn-admin-data-empty"><i class="bi bi-folder2-open" aria-hidden="true"></i><strong>Belum ada data guru</strong><span>Tambahkan data guru untuk mulai mengelola akun pendidik.</span></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="sipn-admin-data-no-match" data-admin-search-empty hidden><i class="bi bi-search" aria-hidden="true"></i><strong>Data tidak ditemukan</strong><span>Coba kata kunci lain atau tekan tombol reset.</span></div>
        </div>
    </section>
</div>

<!-- ================= SEMUA MODAL DI LUAR TABEL ================= -->
<?php foreach ($teachers as $row): ?>

<!-- Modal Edit Guru -->
<div class="modal-overlay" id="modalEdit<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5>Edit Data Guru</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id_guru" value="<?= $row['id']; ?>">
                
                <div class="form-group">
                    <label class="form-label">NIP</label>
                    <input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($row['nip']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap & Gelar</label>
                    <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($row['nama']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-select" name="jenis_kelamin" required>
                        <option value="L" <?= $row['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?= $row['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Guru -->
<div class="modal-overlay" id="modalHapus<?= $row['id']; ?>">
    <div class="modal-box">
        <form method="POST">
            <div class="modal-header header-danger">
                <h5>Konfirmasi Hapus</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                <p>Hapus guru <strong><?= htmlspecialchars($row['nama']); ?></strong>?</p>
                <p style="font-size: 0.78rem; color: #dc2626; margin-top: 0.5rem;">Tindakan ini juga menghapus akun login terkait secara permanen.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger" style="background: #dc2626; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<?php endforeach; ?>

<!-- Modal Tambah Guru & Akun -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box modal-lg teacher-modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTambahTitle">
        <form method="POST" class="teacher-form">
            <div class="modal-header header-primary teacher-modal-header">
                <div class="teacher-modal-heading">
                    <span class="teacher-modal-icon" aria-hidden="true">
                        <i class="bi bi-person-plus-fill"></i>
                    </span>
                    <div class="teacher-modal-heading-copy">
                        <span class="teacher-modal-eyebrow">MANAJEMEN PENDIDIK</span>
                        <h5 id="modalTambahTitle">Tambah Guru Baru</h5>
                        <p>Lengkapi informasi guru dan akun untuk mengakses portal.</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn teacher-modal-close" data-modal-close aria-label="Tutup form">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>

            <div class="modal-body teacher-modal-body">
                <input type="hidden" name="aksi" value="tambah">

                <section class="teacher-form-section" aria-labelledby="teacherIdentityHeading">
                    <div class="teacher-section-heading">
                        <span class="teacher-section-icon" aria-hidden="true"><i class="bi bi-person-vcard"></i></span>
                        <div>
                            <h6 id="teacherIdentityHeading">Informasi Guru</h6>
                            <p>Masukkan identitas sesuai data resmi guru.</p>
                        </div>
                    </div>

                    <div class="teacher-form-grid">
                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherNip">Nomor Induk Pegawai <span class="teacher-required">Wajib</span></label>
                            <input type="text" class="form-control" id="teacherNip" name="nip" placeholder="Contoh: 198506152010011001" autocomplete="off" required>
                            <small class="teacher-field-help">Gunakan NIP yang valid dan belum terdaftar.</small>
                        </div>

                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherGender">Jenis Kelamin <span class="teacher-required">Wajib</span></label>
                            <select class="form-select" id="teacherGender" name="jenis_kelamin" required>
                                <option value="" disabled selected>Pilih jenis kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group teacher-field teacher-name-field">
                        <label class="form-label" for="teacherName">Nama Lengkap dan Gelar <span class="teacher-required">Wajib</span></label>
                        <input type="text" class="form-control" id="teacherName" name="nama" placeholder="Contoh: Budi Santoso, S.Kom." autocomplete="name" required>
                        <small class="teacher-field-help">Tuliskan nama beserta gelar jika ada.</small>
                    </div>
                </section>

                <section class="teacher-form-section teacher-account-section" aria-labelledby="teacherAccountHeading">
                    <div class="teacher-section-heading">
                        <span class="teacher-section-icon" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
                        <div>
                            <h6 id="teacherAccountHeading">Akun Login</h6>
                            <p>Kredensial ini digunakan guru untuk masuk ke sistem.</p>
                        </div>
                    </div>

                    <div class="teacher-form-grid">
                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherUsername">Username <span class="teacher-required">Wajib</span></label>
                            <input type="text" class="form-control" id="teacherUsername" name="username" placeholder="Buat username" autocomplete="off" required>
                            <small class="teacher-field-help">Username harus unik dan belum digunakan.</small>
                        </div>

                        <div class="form-group teacher-field">
                            <label class="form-label" for="teacherPassword">Kata Sandi <span class="teacher-required">Wajib</span></label>
                            <input type="password" class="form-control" id="teacherPassword" name="password" placeholder="Buat kata sandi" autocomplete="new-password" required>
                            <small class="teacher-field-help">Berikan kata sandi awal kepada guru secara aman.</small>
                        </div>
                    </div>
                </section>

                <p class="teacher-form-note"><i class="bi bi-info-circle" aria-hidden="true"></i> Pastikan seluruh informasi sudah benar sebelum menyimpan data.</p>
            </div>

            <div class="modal-footer teacher-modal-footer">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-check2-circle" aria-hidden="true"></i> Simpan Data Guru
                </button>
            </div>
        </form>
    </div>
</div>

