<?php
/**
 * View profile.
 * Variabel data disiapkan oleh controller profile.php.
 * File ini hanya menangani presentasi; koneksi dan otorisasi tetap pada controller.
 */
?>
<div class="profile-container-centered">
    
    <!-- Profil Card Berada di Tengah -->
    <div class="profile-card-centered">
        
        <!-- Bagian Atas: Avatar, Judul, Tombol Edit -->
        <div class="profile-header-centered">
            
            <button class="btn-primary-outline btn-edit-profile-absolute" data-modal-target="#modalOpsiEdit">
                <i class="bi bi-pencil-square"></i> Edit Profil
            </button>

        <div class="avatar-circle-centered" style="overflow: hidden; padding: 0;">
    <?php if (!empty($data_user['foto'])): ?>
        <img src="<?= BASE_URL; ?>assets/img/profil/<?= htmlspecialchars($data_user['foto']); ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
    <?php else: ?>
        <i class="bi bi-person"></i>
    <?php endif; ?>
        </div>
            
            <h2 class="profile-name"><?= htmlspecialchars($nama_lengkap); ?></h2>
            <div class="profile-role-badge">
                <i class="bi bi-shield-check"></i> Hak Akses: <?= ucfirst($role); ?>
            </div>
        </div>

        <!-- Bagian Bawah: Informasi Detail -->
        <div class="profile-body-centered">
            
            <div class="info-row">
                <div class="info-label"><i class="bi bi-person-badge"></i> Username Akun</div>
                <div class="info-value"><?= htmlspecialchars($data_user['username']); ?></div>
            </div>

            <?php if ($role === 'guru'): ?>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-hash"></i> NIP Guru</div>
                    <div class="info-value" style="font-family: monospace; font-size: 1.15rem;"><?= htmlspecialchars($identitas_id); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-gender-ambiguous"></i> Jenis Kelamin</div>
                    <div class="info-value"><?= $jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'; ?></div>
                </div>
            <?php endif; ?>

            <?php if ($role === 'siswa'): ?>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-hash"></i> NIS Siswa</div>
                    <div class="info-value" style="font-family: monospace; font-size: 1.15rem;"><?= htmlspecialchars($identitas_id); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-bookmark-star"></i> Kelas / Rombel</div>
                    <div class="info-value"><?= htmlspecialchars($kelas); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="bi bi-gender-ambiguous"></i> Jenis Kelamin</div>
                    <div class="info-value"><?= $jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'; ?></div>
                </div>
            <?php endif; ?>
            
            <div class="info-row">
                <div class="info-label"><i class="bi bi-shield-lock"></i> Keamanan Sandi</div>
                <div class="info-value" style="color: var(--text-muted);">•••••••• (Terenkripsi)</div>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL 1: PILIHAN OPSI EDIT ================= -->
<div class="modal-overlay" id="modalOpsiEdit">
    <div class="modal-box" style="max-width: 600px;">
        <div class="modal-header header-primary">
            <h5>Pilih Bagian yang Ingin Diedit</h5>
            <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
        </div>
        <div class="modal-body">
            <div class="edit-options-grid">
                
                <!-- Opsi Edit Informasi -->
                <div class="edit-option-card" role="button" tabindex="0" data-profile-modal-switch data-open-modal="#modalEditInformasi" aria-controls="modalEditInformasi">
                    <i class="bi bi-card-text"></i>
                    <h6>Edit Data Informasi</h6>
                    <p>Ubah nama, NIS/NIP, kelas, username, atau kata sandi.</p>
                </div>

                <!-- Opsi Edit Foto -->
                <div class="edit-option-card" role="button" tabindex="0" data-profile-modal-switch data-open-modal="#modalEditFoto" aria-controls="modalEditFoto">
                    <i class="bi bi-camera-fill"></i>
                    <h6>Ganti Foto Profil</h6>
                    <p>Unggah atau perbarui foto profil identitas Anda.</p>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL 2: FORM EDIT INFORMASI MENYELURUH ================= -->
<div class="modal-overlay" id="modalEditInformasi">
    <div class="modal-box modal-lg" style="max-width: 650px;">
        <form method="POST">
            <div class="modal-header header-warning">
                <h5><i class="bi bi-pencil-square"></i> Perbarui Data Profil (<?= ucfirst($role); ?>)</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                
                <!-- Kredensial Akun -->
                <div class="modal-section-title">
                    <i class="bi bi-shield-lock-fill"></i> Data Kredensial Akun
                </div>
                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 2rem;">
                    <div class="auth-input-group" style="margin: 0;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; display: block;">Username Login</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-at field-icon"></i>
                            <input type="text" name="username" value="<?= htmlspecialchars($data_user['username']); ?>" required>
                        </div>
                    </div>
                    <div class="auth-input-group" style="margin: 0;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; display: block;">Password Baru</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-key-fill field-icon"></i>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak diubah...">
                        </div>
                    </div>
                </div>

                <!-- Identitas Personal -->
                <?php if ($role !== 'admin'): ?>
                <div class="modal-section-title">
                    <i class="bi bi-person-vcard-fill"></i> Identitas Personal
                </div>
                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    
                    <?php if ($role === 'guru'): ?>
                        <div class="auth-input-group" style="margin: 0;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; display: block;">NIP Pegawai</label>
                            <div class="auth-input-wrapper">
                                <i class="bi bi-hash field-icon"></i>
                                <input type="text" name="nip" value="<?= htmlspecialchars($identitas_id); ?>" required>
                            </div>
                        </div>
                    <?php elseif ($role === 'siswa'): ?>
                        <div class="auth-input-group" style="margin: 0;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; display: block;">NIS Siswa</label>
                            <div class="auth-input-wrapper">
                                <i class="bi bi-hash field-icon"></i>
                                <input type="text" name="nis" value="<?= htmlspecialchars($identitas_id); ?>" required>
                            </div>
                        </div>
                        <div class="auth-input-group" style="margin: 0;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; display: block;">Kelas / Rombel</label>
                            <div class="auth-input-wrapper">
                                <i class="bi bi-bookmark-star-fill field-icon"></i>
                                <input type="text" name="kelas" value="<?= htmlspecialchars($kelas); ?>" required>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="auth-input-group" style="grid-column: 1 / -1; margin: 0;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; display: block;">Nama Lengkap</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-person-lines-fill field-icon"></i>
                            <input type="text" name="nama" value="<?= htmlspecialchars($nama_lengkap); ?>" required>
                        </div>
                    </div>

                    <div class="auth-input-group" style="grid-column: 1 / -1; margin: 0;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.4rem; display: block;">Jenis Kelamin</label>
                        <div class="auth-input-wrapper">
                            <i class="bi bi-gender-ambiguous field-icon"></i>
                            <select name="jenis_kelamin" required style="width: 100%; padding: 0.75rem 0.8rem 0.75rem 2.6rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; font-size: 0.9rem; color: var(--text-dark); outline: none; appearance: auto;">
                                <option value="L" <?= $jenis_kelamin === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?= $jenis_kelamin === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <div class="modal-footer" style="padding: 1.25rem 2rem;">
                <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                <button type="submit" name="update_informasi" class="btn-warning" style="padding: 0.65rem 1.25rem; font-size: 0.9rem; border-radius: 10px;">
                    <i class="bi bi-check2-circle"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 3: FORM EDIT FOTO (DENGAN LIVE PREVIEW) ================= -->
<div class="modal-overlay" id="modalEditFoto">
    <div class="modal-box" style="max-width: 480px;">
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-header header-primary">
                <h5><i class="bi bi-camera-fill"></i> Ganti Foto Profil</h5>
                <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
            </div>
            
            <div class="modal-body" style="padding: 1.75rem 1.5rem;">
                
                <!-- Area Dropzone Unggah Berkas -->
                <div class="upload-dropzone" style="position: relative;">
                    <!-- Input File (Transparan menutupi seluruh area dropzone) -->
                    <input type="file" id="inputFotoProfil" name="foto_profil" accept="image/png, image/jpeg, image/jpg" required 
                           style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 5;"
                           data-profile-photo-input>
                    
                    <!-- Tampilan Awal (Ikon Awan) -->
                    <div id="dropzoneContentAwal">
                        <div class="dropzone-icon-box" style="margin: 0 auto 0.85rem auto;">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>
                        <div class="dropzone-title">Klik atau Seret Foto Ke Sini</div>
                        <p class="dropzone-subtitle">Format didukung: <strong>JPG, PNG</strong> (Maksimal 2MB)</p>
                    </div>

                    <!-- Tampilan Pratinjau (Foto Muncul di Sini) -->
                    <div class="preview-image-wrapper" id="wadahPreview">
                        <img id="gambarPreview" src="" alt="Pratinjau Foto">
                    </div>
                    
                    <!-- Badge Nama File -->
                    <div class="file-preview-badge" id="badgeNamaFile">
                        <i class="bi bi-file-earmark-image-fill"></i>
                        <span id="teksNamaFile">-</span>
                    </div>
                </div>

                <!-- Alert Informasi Tambahan -->
                <div class="alert alert-info" style="margin-top: 1.25rem; margin-bottom: 0; padding: 0.85rem 1rem; border-radius: 10px; font-size: 0.8rem; line-height: 1.4; display: flex; align-items: flex-start; gap: 0.65rem;">
                    <i class="bi bi-info-circle-fill" style="font-size: 1.05rem; flex-shrink: 0; margin-top: 1px;"></i>
                    <div>
                        Klik tombol <strong>Unggah Foto</strong> di bawah untuk menyimpan perubahan. Pastikan kolom <code>foto</code> telah ditambahkan di tabel <code>users</code>.
                    </div>
                </div>

            </div>

            <div class="modal-footer" style="padding: 1rem 1.5rem; background: var(--bg-surface); border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-modal-close>
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="submit" name="update_foto" class="btn btn-primary-custom">
                    <i class="bi bi-upload"></i> Unggah Foto
                </button>
            </div>
        </form>
    </div>
</div>

