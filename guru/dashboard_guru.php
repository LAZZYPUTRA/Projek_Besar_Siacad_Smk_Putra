<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['guru']);

$id_user = (int) ($_SESSION['user_id'] ?? 0);

$query_guru = mysqli_query($conn, "SELECT * FROM guru WHERE user_id = '$id_user'");
$guru = $query_guru ? mysqli_fetch_assoc($query_guru) : null;
$id_guru = (int) ($guru['id'] ?? 0);
$nama_guru = $guru['nama'] ?? ($_SESSION['username'] ?? 'Guru');
$nip = $guru['nip'] ?? '-';

$q_mapel = mysqli_query($conn, "SELECT COUNT(*) AS total FROM mapel WHERE guru_id = '$id_guru'");
$row_mapel = $q_mapel ? mysqli_fetch_assoc($q_mapel) : null;
$total_mapel = $q_mapel ? (int) ($row_mapel['total'] ?? 0) : null;

$q_siswa = mysqli_query($conn, "SELECT COUNT(*) AS total FROM siswa");
$row_siswa = $q_siswa ? mysqli_fetch_assoc($q_siswa) : null;
$total_siswa = $q_siswa ? (int) ($row_siswa['total'] ?? 0) : null;

$q_nilai_input = mysqli_query($conn, "
    SELECT COUNT(nr.id) AS total
    FROM nilai_rapor nr
    JOIN mapel m ON nr.mapel_id = m.id
    WHERE m.guru_id = '$id_guru'
");
$row_nilai = $q_nilai_input ? mysqli_fetch_assoc($q_nilai_input) : null;
$total_nilai_input = $q_nilai_input ? (int) ($row_nilai['total'] ?? 0) : null;

$query_mapel_list = mysqli_query($conn, "SELECT id, kode_mapel, nama_mapel FROM mapel WHERE guru_id = '$id_guru' ORDER BY nama_mapel ASC");

$dashboard_query_error = !$query_guru || !$q_mapel || !$q_siswa || !$q_nilai_input || !$query_mapel_list;

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/dashboard_guru_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
