<?php
require_once __DIR__ . '/config/koneksi_database.php';
require_once __DIR__ . '/includes/helper_fungsi.php';
require_once __DIR__ . '/includes/helper_cek_login.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 1. Ambil Data Gabungan (Users + Guru/Siswa)
$query_user = mysqli_query($conn, "SELECT username, role, foto FROM users WHERE id = '$user_id'");
$data_user = mysqli_fetch_assoc($query_user);

$nama_lengkap = $data_user['username'];
$identitas_id = '';
$jenis_kelamin = '';
$kelas = '';

if ($role === 'guru') {
    $q_detail = mysqli_query($conn, "SELECT nip, nama, jenis_kelamin FROM guru WHERE user_id = '$user_id'");
    if ($d = mysqli_fetch_assoc($q_detail)) {
        $nama_lengkap = $d['nama'];
        $identitas_id = $d['nip'];
        $jenis_kelamin = $d['jenis_kelamin'];
    }
} elseif ($role === 'siswa') {
    $q_detail = mysqli_query($conn, "SELECT nis, nama, kelas, jenis_kelamin FROM siswa WHERE user_id = '$user_id'");
    if ($d = mysqli_fetch_assoc($q_detail)) {
        $nama_lengkap = $d['nama'];
        $identitas_id = $d['nis'];
        $kelas = $d['kelas'];
        $jenis_kelamin = $d['jenis_kelamin'];
    }
} elseif ($role === 'admin') {
    $nama_lengkap = 'Administrator Sistem';
}

// 2. Proses Update Informasi Menyeluruh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_informasi'])) {
    $username_baru = sanitize($_POST['username']);
    $password_baru = trim($_POST['password']);
    
    mysqli_begin_transaction($conn);
    try {
        // A. Update tabel users
        $q_user_update = "UPDATE users SET username = '$username_baru'";
        if (!empty($password_baru)) {
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $q_user_update .= ", password = '$password_hash'";
        }
        $q_user_update .= " WHERE id = '$user_id'";
        mysqli_query($conn, $q_user_update);
        
        // B. Update tabel spesifik sesuai role
        if ($role === 'guru') {
            $nip = sanitize($_POST['nip']);
            $nama = sanitize($_POST['nama']);
            $jk = sanitize($_POST['jenis_kelamin']);
            mysqli_query($conn, "UPDATE guru SET nip = '$nip', nama = '$nama', jenis_kelamin = '$jk' WHERE user_id = '$user_id'");
        } elseif ($role === 'siswa') {
            $nis = sanitize($_POST['nis']);
            $nama = sanitize($_POST['nama']);
            $kls = sanitize($_POST['kelas']);
            $jk = sanitize($_POST['jenis_kelamin']);
            mysqli_query($conn, "UPDATE siswa SET nis = '$nis', nama = '$nama', kelas = '$kls', jenis_kelamin = '$jk' WHERE user_id = '$user_id'");
        }

        mysqli_commit($conn);
        $_SESSION['username'] = $username_baru; // Update session
        set_flash_message('success', 'Informasi profil berhasil diperbarui secara menyeluruh!');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        set_flash_message('danger', 'Gagal memperbarui profil: ' . $e->getMessage());
    }
    
    header("Location: profile.php");
    exit;
}

// 3. Proses Update Foto (Upload Asli ke Direktori & Database)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_foto'])) {
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === 0) {
        $file = $_FILES['foto_profil'];
        $nama_file = $file['name'];
        $ukuran_file = $file['size'];
        $tmp_name = $file['tmp_name'];
        
        $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png'];
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        
        // Cek ekstensi file
        if (in_array($ekstensi, $ekstensi_diperbolehkan)) {
            // Cek ukuran file (Maksimal 2MB = 2097152 bytes)
            if ($ukuran_file <= 2097152) {
                // Buat nama file unik agar tidak bentrok
                $nama_file_baru = 'profil_' . $role . '_' . $user_id . '_' . time() . '.' . $ekstensi;
                
                // Tentukan lokasi folder penyimpanan (Otomatis dibuat jika belum ada)
                $direktori = __DIR__ . '/assets/img/profil/';
                if (!is_dir($direktori)) {
                    mkdir($direktori, 0777, true);
                }
                
                $path_simpan = $direktori . $nama_file_baru;
                
                // Pindahkan file dari memori sementara ke folder tujuan
                if (move_uploaded_file($tmp_name, $path_simpan)) {
                    
                    // (Opsional & Aman): Hapus foto lama di folder agar server tidak bengkak
                    if (!empty($data_user['foto']) && file_exists($direktori . $data_user['foto'])) {
                        unlink($direktori . $data_user['foto']);
                    }
                    
                    // Simpan nama file baru ke database
                    $stmt = mysqli_prepare($conn, "UPDATE users SET foto = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "si", $nama_file_baru, $user_id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        set_flash_message('success', 'Foto profil berhasil diperbarui!');
                    } else {
                        set_flash_message('danger', 'File terunggah, tapi gagal menyimpan ke database.');
                    }
                } else {
                    set_flash_message('danger', 'Sistem gagal memindahkan file foto ke direktori server.');
                }
            } else {
                set_flash_message('danger', 'Gagal! Ukuran foto terlalu besar (Maksimal 2MB).');
            }
        } else {
            set_flash_message('danger', 'Gagal! Format file tidak didukung. Gunakan JPG atau PNG.');
        }
    } else {
        set_flash_message('danger', 'Terjadi kesalahan atau tidak ada file yang dipilih.');
    }
    
    header("Location: profile.php");
    exit;
}
require_once __DIR__ . '/includes/template_header.php';
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
                <div class="edit-option-card" onclick="document.getElementById('modalOpsiEdit').classList.remove('is-active'); document.getElementById('modalEditInformasi').classList.add('is-active');">
                    <i class="bi bi-card-text"></i>
                    <h6>Edit Data Informasi</h6>
                    <p>Ubah nama, NIS/NIP, kelas, username, atau kata sandi.</p>
                </div>

                <!-- Opsi Edit Foto -->
                <div class="edit-option-card" onclick="document.getElementById('modalOpsiEdit').classList.remove('is-active'); document.getElementById('modalEditFoto').classList.add('is-active');">
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
                           onchange="previewFoto(this)">
                    
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

<!-- SCRIPT LIVE PREVIEW FOTO -->
<script>
function previewFoto(input) {
    const kontenAwal = document.getElementById('dropzoneContentAwal');
    const wadahPreview = document.getElementById('wadahPreview');
    const gambarPreview = document.getElementById('gambarPreview');
    const badgeFile = document.getElementById('badgeNamaFile');
    const teksNamaFile = document.getElementById('teksNamaFile');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Sembunyikan ikon awan dan tampilkan foto
            kontenAwal.style.display = 'none';
            wadahPreview.style.display = 'block';
            gambarPreview.src = e.target.result;
            
            // Tampilkan badge nama file
            badgeFile.style.display = 'inline-flex';
            teksNamaFile.textContent = input.files[0].name;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        // Kembalikan ke tampilan awal jika pengguna batal memilih file
        kontenAwal.style.display = 'block';
        wadahPreview.style.display = 'none';
        badgeFile.style.display = 'none';
        gambarPreview.src = "";
    }
}
</script>

<?php require_once __DIR__ . '/includes/template_footer.php'; ?>