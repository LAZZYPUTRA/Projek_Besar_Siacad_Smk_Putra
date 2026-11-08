<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';
check_role(['admin']);

$e = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

// Proses penambahan dan penghapusan penugasan tetap menggunakan tabel mengajar.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = (string) $_POST['aksi'];

    if ($aksi === 'tambah') {
        $mapel_id = (int) ($_POST['mapel_id'] ?? 0);
        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);

        if ($mapel_id <= 0 || $siswa_id <= 0) {
            set_flash_message('danger', 'Pilih mata pelajaran dan siswa yang valid.');
        } else {
            // Pastikan kedua data induk tersedia sebelum membuat relasi.
            $stmt_validasi = mysqli_prepare(
                $conn,
                'SELECT m.id FROM mapel m CROSS JOIN siswa s WHERE m.id = ? AND s.id = ? LIMIT 1'
            );

            if (!$stmt_validasi) {
                set_flash_message('danger', 'Validasi penugasan tidak dapat dijalankan.');
            } else {
                mysqli_stmt_bind_param($stmt_validasi, 'ii', $mapel_id, $siswa_id);
                mysqli_stmt_execute($stmt_validasi);
                $validasi_result = mysqli_stmt_get_result($stmt_validasi);
                $data_valid = $validasi_result && mysqli_num_rows($validasi_result) === 1;
                mysqli_stmt_close($stmt_validasi);

                if (!$data_valid) {
                    set_flash_message('danger', 'Mata pelajaran atau siswa yang dipilih tidak ditemukan.');
                } else {
                    // Cegah relasi siswa-mapel ganda.
                    $stmt_cek = mysqli_prepare(
                        $conn,
                        'SELECT id FROM mengajar WHERE mapel_id = ? AND siswa_id = ? LIMIT 1'
                    );

                    if (!$stmt_cek) {
                        set_flash_message('danger', 'Pemeriksaan penugasan gagal dijalankan.');
                    } else {
                        mysqli_stmt_bind_param($stmt_cek, 'ii', $mapel_id, $siswa_id);
                        mysqli_stmt_execute($stmt_cek);
                        $cek_result = mysqli_stmt_get_result($stmt_cek);
                        $sudah_ada = $cek_result && mysqli_num_rows($cek_result) > 0;
                        mysqli_stmt_close($stmt_cek);

                        if ($sudah_ada) {
                            set_flash_message('warning', 'Siswa tersebut sudah terdaftar pada mata pelajaran ini.');
                        } else {
                            $stmt = mysqli_prepare($conn, 'INSERT INTO mengajar (mapel_id, siswa_id) VALUES (?, ?)');
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, 'ii', $mapel_id, $siswa_id);
                                if (mysqli_stmt_execute($stmt)) {
                                    set_flash_message('success', 'Penugasan siswa berhasil ditambahkan!');
                                } else {
                                    set_flash_message('danger', 'Gagal menyimpan penugasan siswa.');
                                }
                                mysqli_stmt_close($stmt);
                            } else {
                                set_flash_message('danger', 'Penugasan tidak dapat disimpan saat ini.');
                            }
                        }
                    }
                }
            }
        }
    } elseif ($aksi === 'hapus') {
        $id_mengajar = (int) ($_POST['id_mengajar'] ?? 0);

        if ($id_mengajar <= 0) {
            set_flash_message('danger', 'Penugasan yang dipilih tidak valid.');
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM mengajar WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id_mengajar);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    set_flash_message('success', 'Penugasan berhasil dihapus!');
                } else {
                    set_flash_message('danger', 'Gagal menghapus penugasan. Data mungkin sudah tidak tersedia.');
                }
                mysqli_stmt_close($stmt);
            } else {
                set_flash_message('danger', 'Permintaan penghapusan tidak dapat diproses.');
            }
        }
    } else {
        set_flash_message('danger', 'Aksi penugasan tidak dikenali.');
    }

    header('Location: data_mengajar.php');
    exit();
}

// Daftar penugasan beserta identitas mapel, guru, dan siswa.
$query = "
    SELECT
        mg.id,
        mg.mapel_id,
        mg.siswa_id,
        m.nama_mapel,
        m.kode_mapel,
        g.nama AS nama_guru,
        s.nama AS nama_siswa,
        s.nis,
        s.kelas
    FROM mengajar mg
    JOIN mapel m ON mg.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    JOIN siswa s ON mg.siswa_id = s.id
    ORDER BY m.nama_mapel ASC, s.kelas ASC, s.nama ASC
";
$result_penugasan = mysqli_query($conn, $query);
$penugasan = [];
if ($result_penugasan) {
    while ($row = mysqli_fetch_assoc($result_penugasan)) {
        $penugasan[] = $row;
    }
}

// Opsi modal disusun sebagai array agar nilai dari database selalu di-escape saat dirender.
$mapel_list = [];
$q_mapel = mysqli_query(
    $conn,
    'SELECT m.id, m.kode_mapel, m.nama_mapel, g.nama AS nama_guru FROM mapel m LEFT JOIN guru g ON m.guru_id = g.id ORDER BY m.nama_mapel ASC'
);
if ($q_mapel) {
    while ($row = mysqli_fetch_assoc($q_mapel)) {
        $mapel_list[] = $row;
    }
}

$siswa_list = [];
$q_siswa = mysqli_query($conn, 'SELECT id, nis, nama, kelas FROM siswa ORDER BY kelas ASC, nama ASC');
if ($q_siswa) {
    while ($row = mysqli_fetch_assoc($q_siswa)) {
        $siswa_list[] = $row;
    }
}

$total_penugasan = count($penugasan);
$unique_mapel = [];
$unique_siswa = [];
foreach ($penugasan as $row) {
    $unique_mapel[(int) $row['mapel_id']] = true;
    $unique_siswa[(int) $row['siswa_id']] = true;
}
$jumlah_mapel_terpakai = count($unique_mapel);
$jumlah_siswa_terdaftar = count($unique_siswa);
$query_error = !$result_penugasan || !$q_mapel || !$q_siswa;

require_once __DIR__ . '/../includes/template_header.php';
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

<script src="<?= BASE_URL; ?>assets/js/admin-table-search.js?v=admin-academic-20261108" defer></script>
<?php require_once __DIR__ . '/../includes/template_footer.php'; ?>