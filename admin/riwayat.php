<?php
require_once __DIR__ . '/../config/koneksi_database.php';
require_once __DIR__ . '/../includes/helper_fungsi.php';
require_once __DIR__ . '/../includes/helper_cek_login.php';

check_role(['admin']);

$escape = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

// Halaman ini mengikuti sumber data yang tersedia pada proyek: akun terbaru
// dari tabel users, diurutkan berdasarkan ID. Ini bukan audit log aktivitas.
$summary_query = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END), 0) AS total_admin,
        COALESCE(SUM(CASE WHEN role = 'guru' THEN 1 ELSE 0 END), 0) AS total_guru,
        COALESCE(SUM(CASE WHEN role = 'siswa' THEN 1 ELSE 0 END), 0) AS total_siswa
    FROM users
");

$summary = $summary_query ? mysqli_fetch_assoc($summary_query) : null;

$query_riwayat = mysqli_query(
    $conn,
    'SELECT id, username, role FROM users ORDER BY id DESC LIMIT 50'
);

$riwayat = [];
if ($query_riwayat) {
    while ($row = mysqli_fetch_assoc($query_riwayat)) {
        $riwayat[] = $row;
    }
}

$query_error = !$summary_query || !$query_riwayat;
$total_ditampilkan = count($riwayat);

$role_config = [
    'admin' => [
        'label' => 'Administrator',
        'class' => 'is-admin',
        'icon'  => 'bi-shield-lock-fill',
    ],
    'guru' => [
        'label' => 'Guru',
        'class' => 'is-guru',
        'icon'  => 'bi-person-workspace',
    ],
    'siswa' => [
        'label' => 'Siswa',
        'class' => 'is-siswa',
        'icon'  => 'bi-mortarboard-fill',
    ],
];

require_once __DIR__ . '/../includes/template_header.php';
require __DIR__ . '/views/riwayat_view.php';
require_once __DIR__ . '/../includes/template_footer.php';
