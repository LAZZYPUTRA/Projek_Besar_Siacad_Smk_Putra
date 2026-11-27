<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['siswa']);

$user_id = (int) ($_SESSION['user_id'] ?? 0);

// Ambil data siswa berdasarkan akun yang sedang login.
$query_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = '$user_id'");
$siswa = $query_siswa ? mysqli_fetch_assoc($query_siswa) : null;

if (!$siswa) {
    die("Data profil siswa tidak ditemukan. Hubungi Administrator.");
}

$id_siswa   = (int) $siswa['id'];
$nama_siswa = $siswa['nama'];
$nis        = $siswa['nis'];
$kelas      = $siswa['kelas'];

// Hitung statistik berdasarkan data akademik siswa.
$query_stat = mysqli_query($conn, "SELECT COUNT(*) AS total_nilai FROM nilai_rapor WHERE siswa_id = '$id_siswa'");
$stat = $query_stat ? mysqli_fetch_assoc($query_stat) : null;
$total_nilai = $query_stat ? (int) ($stat['total_nilai'] ?? 0) : null;

$query_mapel = mysqli_query($conn, "SELECT COUNT(*) AS total_mapel FROM mengajar WHERE siswa_id = '$id_siswa'");
$stat_mapel = $query_mapel ? mysqli_fetch_assoc($query_mapel) : null;
$total_mapel = $query_mapel ? (int) ($stat_mapel['total_mapel'] ?? 0) : null;

// Ambil daftar nilai rapor untuk ditampilkan pada dashboard.
$query_nilai_list = mysqli_query($conn, "
    SELECT nr.*, m.kode_mapel, m.nama_mapel, g.nama AS nama_guru
    FROM nilai_rapor nr
    JOIN mapel m ON nr.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    WHERE nr.siswa_id = '$id_siswa'
    ORDER BY m.nama_mapel ASC
");

$dashboard_query_error = !$query_siswa || !$query_stat || !$query_mapel || !$query_nilai_list;

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/dashboard_siswa_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
