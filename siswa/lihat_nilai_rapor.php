<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['siswa']);

$user_id = (int) ($_SESSION['user_id'] ?? 0);

// Ambil identitas siswa dari akun yang sedang aktif.
$query_siswa = mysqli_query(
    $conn,
    "SELECT id, nama, nis, kelas FROM siswa WHERE user_id = '$user_id' LIMIT 1"
);
$siswa = $query_siswa ? mysqli_fetch_assoc($query_siswa) : null;
$id_siswa = (int) ($siswa['id'] ?? 0);

// Ambil nilai rapor beserta mata pelajaran dan guru pengampu.
$query_nilai = mysqli_query($conn, "
    SELECT
        nr.nilai_kkm,
        nr.nilai_pengetahuan,
        nr.nilai_keterampilan,
        nr.Keterangan,
        m.kode_mapel,
        m.nama_mapel,
        g.nama AS nama_guru
    FROM nilai_rapor nr
    JOIN mapel m ON nr.mapel_id = m.id
    JOIN guru g ON m.guru_id = g.id
    WHERE nr.siswa_id = '$id_siswa'
    ORDER BY m.nama_mapel ASC
");

$total_mapel = $query_nilai ? mysqli_num_rows($query_nilai) : 0;

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/lihat_nilai_rapor_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
