<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $nis      = sanitize($_POST['nis']);
        $nama     = sanitize($_POST['nama']);
        $kelas    = sanitize($_POST['kelas']);
        $jk       = sanitize($_POST['jenis_kelamin']);
        $username = sanitize($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role     = 'siswa';

        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' UNION SELECT id FROM siswa WHERE nis='$nis'");
        if (mysqli_num_rows($cek) > 0) {
            set_flash_message('danger', 'Gagal! Username atau NIS sudah terdaftar.');
        } else {
            mysqli_begin_transaction($conn);
            try {
                $stmt1 = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt1, "sss", $username, $password, $role);
                mysqli_stmt_execute($stmt1);
                $user_id = mysqli_insert_id($conn);

                $stmt2 = mysqli_prepare($conn, "INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, "ssssi", $nis, $nama, $kelas, $jk, $user_id);
                mysqli_stmt_execute($stmt2);

                mysqli_commit($conn);
                set_flash_message('success', 'Data Siswa dan Akun berhasil ditambahkan!');
            } catch (Exception $e) {
                mysqli_rollback($conn);
                set_flash_message('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            }
        }
    } 
    elseif ($aksi === 'edit') {
        $id_siswa = (int)$_POST['id_siswa'];
        $nis      = sanitize($_POST['nis']);
        $nama     = sanitize($_POST['nama']);
        $kelas    = sanitize($_POST['kelas']);
        $jk       = sanitize($_POST['jenis_kelamin']);

        $stmt = mysqli_prepare($conn, "UPDATE siswa SET nis=?, nama=?, kelas=?, jenis_kelamin=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nis, $nama, $kelas, $jk, $id_siswa);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Siswa berhasil diperbarui!');
        } else {
            set_flash_message('danger', 'Gagal memperbarui data Siswa.');
        }
    }
    elseif ($aksi === 'hapus') {
        $user_id = (int)$_POST['user_id'];
        
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Data Siswa beserta akun berhasil dihapus!');
        } else {
            set_flash_message('danger', 'Gagal menghapus data.');
        }
    }
    header("Location: data_siswa.php");
    exit();
}

$query = "SELECT s.*, u.username FROM siswa s JOIN users u ON s.user_id = u.id ORDER BY s.kelas ASC, s.nama ASC";
$result = mysqli_query($conn, $query);
$students = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

$total_siswa = count($students);
$total_kelas = count(array_unique(array_map(static fn($row) => (string) ($row['kelas'] ?? ''), $students)));
$total_siswa_laki = count(array_filter($students, static fn($row) => ($row['jenis_kelamin'] ?? '') === 'L'));
$total_siswa_perempuan = count(array_filter($students, static fn($row) => ($row['jenis_kelamin'] ?? '') === 'P'));
$data_siswa_error = !$result;

require_once __DIR__ . '/../includes/template_header.php';
?>

<div class="sipn-admin-data-page sipn-admin-data-page--siswa">
    <header class="sipn-admin-data-hero sipn-admin-data-enter" style="--admin-enter-delay: 0ms;">
        <div class="sipn-admin-data-hero__copy">
            <span class="sipn-admin-data-eyebrow"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i> Administrasi Peserta Didik</span>
            <h1>Manajemen Data Siswa</h1>
            <p>Kelola identitas peserta didik dan akun akses akademik SIPN secara terstruktur.</p>
            <div class="sipn-admin-data-hero__meta">
                <span><i class="bi bi-people-fill" aria-hidden="true"></i> <?= number_format($total_siswa, 0, ',', '.'); ?> siswa terdaftar</span>
                <span><i class="bi bi-building" aria-hidden="true"></i> <?= number_format($total_kelas, 0, ',', '.'); ?> kelas tercatat</span>
            </div>
        </div>
        <button type="button" class="sipn-admin-data-primary" data-modal-target="#modalTambah">
            <i class="bi bi-person-plus-fill" aria-hidden="true"></i> Tambah Siswa Baru
        </button>
        <span class="sipn-admin-data-hero__ornament" aria-hidden="true"><i class="bi bi-backpack-fill"></i></span>
    </header>

    <?php if ($data_siswa_error): ?>
        <div class="sipn-admin-data-notice" role="alert"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> Data siswa gagal dimuat. Periksa koneksi database, lalu muat ulang halaman.</div>
    <?php endif; ?>

    <section class="sipn-admin-data-stats sipn-admin-data-stats--three" aria-label="Ringkasan data siswa">
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 70ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--blue"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Total Siswa</small><strong><?= number_format($total_siswa, 0, ',', '.'); ?></strong><em>Seluruh peserta didik terdaftar</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 125ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--green"><i class="bi bi-building" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>Kelas Terdaftar</small><strong><?= number_format($total_kelas, 0, ',', '.'); ?></strong><em>Kelompok kelas yang tercatat</em></span>
        </article>
        <article class="sipn-admin-data-stat sipn-admin-data-enter" style="--admin-enter-delay: 180ms;">
            <span class="sipn-admin-data-stat__icon sipn-admin-data-stat__icon--violet"><i class="bi bi-gender-ambiguous" aria-hidden="true"></i></span>
            <span class="sipn-admin-data-stat__body"><small>L / P</small><strong><?= number_format($total_siswa_laki, 0, ',', '.'); ?> / <?= number_format($total_siswa_perempuan, 0, ',', '.'); ?></strong><em>Laki-laki dan perempuan</em></span>
        </article>
    </section>

    <section class="sipn-admin-data-panel sipn-admin-data-enter" style="--admin-enter-delay: 225ms;" aria-labelledby="studentTableTitle">
        <div class="sipn-admin-data-panel__heading">
            <div>
                <span class="sipn-admin-data-kicker">Direktori peserta didik</span>
                <h2 id="studentTableTitle">Daftar Siswa</h2>
                <p>Temukan data berdasarkan NIS, nama, kelas, jenis kelamin, atau username.</p>
            </div>
            <span class="sipn-admin-data-total"><i class="bi bi-person-lines-fill" aria-hidden="true"></i> <?= number_format($total_siswa, 0, ',', '.'); ?> data</span>
        </div>

        <div class="sipn-admin-data-toolbar">
            <label class="sipn-admin-data-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" data-admin-table-search placeholder="Cari NIS, nama siswa, kelas, atau username..." autocomplete="off" aria-label="Cari data siswa">
                <kbd>⌕</kbd>
            </label>
            <button type="button" class="sipn-admin-data-reset" data-admin-search-clear><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset</button>
            <span class="sipn-admin-data-result" aria-live="polite">Menampilkan <strong data-admin-results-count><?= $total_siswa; ?></strong> dari <?= $total_siswa; ?> siswa</span>
        </div>

        <div class="sipn-admin-data-table-wrap">
            <table class="sipn-admin-data-table" data-admin-search-table>
                <thead><tr>
                    <th class="sipn-admin-data-col-number">No.</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jenis Kelamin</th>
                    <th>Username Akun</th>
                    <th class="sipn-admin-data-col-action">Aksi</th>
                </tr></thead>
                <tbody>
                <?php if ($students): ?>
                    <?php foreach ($students as $index => $row): ?>
                    <tr data-admin-search-row style="--admin-row-delay: <?= min($index, 12) * 45; ?>ms;">
                        <td class="sipn-admin-data-number"><?= $index + 1; ?></td>
                        <td><span class="sipn-admin-data-code"><?= htmlspecialchars((string) $row['nis'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><div class="sipn-admin-data-person"><span class="sipn-admin-data-avatar"><i class="bi bi-person-fill" aria-hidden="true"></i></span><strong><?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?></strong></div></td>
                        <td><span class="sipn-admin-data-pill is-blue"><?= htmlspecialchars((string) $row['kelas'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><span class="sipn-admin-data-gender"><?= ($row['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan'; ?></span></td>
                        <td><span class="sipn-admin-data-username"><i class="bi bi-person-circle" aria-hidden="true"></i><?= htmlspecialchars((string) $row['username'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="sipn-admin-data-actions" data-search-ignore>
                            <button type="button" class="sipn-admin-icon-button is-edit" data-modal-target="#modalEdit<?= (int) $row['id']; ?>" title="Edit data siswa" aria-label="Edit <?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                            <button type="button" class="sipn-admin-icon-button is-delete" data-modal-target="#modalHapus<?= (int) $row['id']; ?>" title="Hapus data siswa" aria-label="Hapus <?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="sipn-admin-data-empty"><i class="bi bi-folder2-open" aria-hidden="true"></i><strong>Belum ada data siswa</strong><span>Tambahkan data siswa untuk mulai mengelola akun peserta didik.</span></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="sipn-admin-data-no-match" data-admin-search-empty hidden><i class="bi bi-search" aria-hidden="true"></i><strong>Data tidak ditemukan</strong><span>Coba kata kunci lain atau tekan tombol reset.</span></div>
        </div>
    </section>
</div>

<!-- Modal edit dan hapus diletakkan di luar tabel agar struktur HTML tetap valid. -->
<?php foreach ($students as $row): ?>
<div class="modal-overlay" id="modalEdit<?= (int) $row['id']; ?>">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalEditSiswaTitle<?= (int) $row['id']; ?>">
        <form method="POST">
            <div class="modal-header header-warning"><h5 id="modalEditSiswaTitle<?= (int) $row['id']; ?>">Edit Data Siswa</h5><button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button></div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit"><input type="hidden" name="id_siswa" value="<?= (int) $row['id']; ?>">
                <div class="form-group"><label class="form-label">NIS</label><input type="text" class="form-input" name="nis" value="<?= htmlspecialchars((string) $row['nis'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" class="form-input" name="nama" value="<?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="form-group"><label class="form-label">Kelas</label><input type="text" class="form-input" name="kelas" value="<?= htmlspecialchars((string) $row['kelas'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="form-group"><label class="form-label">Jenis Kelamin</label><select class="form-select" name="jenis_kelamin" required><option value="L" <?= ($row['jenis_kelamin'] ?? '') === 'L' ? 'selected' : ''; ?>>Laki-laki</option><option value="P" <?= ($row['jenis_kelamin'] ?? '') === 'P' ? 'selected' : ''; ?>>Perempuan</option></select></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-modal-close>Batal</button><button type="submit" class="btn btn-warning">Simpan Perubahan</button></div>
        </form>
    </div>
</div>
<div class="modal-overlay" id="modalHapus<?= (int) $row['id']; ?>">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalHapusSiswaTitle<?= (int) $row['id']; ?>">
        <form method="POST">
            <div class="modal-header header-danger"><h5 id="modalHapusSiswaTitle<?= (int) $row['id']; ?>">Konfirmasi Hapus Siswa</h5><button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button></div>
            <div class="modal-body"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="user_id" value="<?= (int) $row['user_id']; ?>"><p>Hapus siswa <strong><?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8'); ?></strong> beserta akun loginnya secara permanen?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-modal-close>Batal</button><button type="submit" class="btn btn-danger">Ya, Hapus</button></div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal Tambah Siswa -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box modal-lg" role="dialog" aria-modal="true" aria-labelledby="modalTambahSiswaTitle">
        <form method="POST">
            <div class="modal-header header-success"><h5 id="modalTambahSiswaTitle">Tambah Siswa &amp; Akun Baru</h5><button type="button" class="modal-close-btn" data-modal-close aria-label="Tutup">&times;</button></div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-row">
                    <div class="form-column">
                        <h6 class="form-subtitle">Data Siswa</h6>
                        <div class="form-group"><label class="form-label">NIS</label><input type="text" class="form-input" name="nis" required></div>
                        <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" class="form-input" name="nama" required></div>
                        <div class="form-group"><label class="form-label">Kelas</label><input type="text" class="form-input" name="kelas" placeholder="Contoh: XII RPL 1" required></div>
                        <div class="form-group"><label class="form-label">Jenis Kelamin</label><select class="form-select" name="jenis_kelamin" required><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                    </div>
                    <div class="form-column">
                        <h6 class="form-subtitle">Data Akun Login</h6>
                        <div class="form-group"><label class="form-label">Username</label><input type="text" class="form-input" name="username" required></div>
                        <div class="form-group"><label class="form-label">Password</label><input type="password" class="form-input" name="password" autocomplete="new-password" required></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-modal-close>Batal</button><button type="submit" class="btn btn-success">Simpan Data</button></div>
        </form>
    </div>
</div>

<script src="<?= BASE_URL; ?>assets/js/admin-table-search.js?v=admin-table-20261108" defer></script>
<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>