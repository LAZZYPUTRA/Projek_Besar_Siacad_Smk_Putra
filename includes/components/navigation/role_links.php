<?php
/**
 * Konfigurasi tautan navigasi berdasarkan role.
 * Data ini dipakai oleh sidebar dan topbar agar rute tidak ditulis dua kali.
 */
$role_navigation_items = [];
$topbar_navigation_items = [];

if ($current_role === 'siswa') {
    $role_navigation_items = [
        [
            'href'  => BASE_URL . 'siswa/dashboard_siswa.php',
            'page'  => 'dashboard_siswa.php',
            'title' => 'Beranda',
            'label' => 'Beranda',
            'icon'  => 'bi-house-door',
        ],
        [
            'href'  => BASE_URL . 'siswa/lihat_nilai_rapor.php',
            'page'  => 'lihat_nilai_rapor.php',
            'title' => 'Lihat Nilai Rapor',
            'label' => 'Lihat Nilai Rapor',
            'icon'  => 'bi-file-earmark-text',
        ],
    ];
    $topbar_navigation_items = $role_navigation_items;
} elseif ($current_role === 'guru') {
    $role_navigation_items = [
        [
            'href'  => BASE_URL . 'guru/dashboard_guru.php',
            'page'  => 'dashboard_guru.php',
            'title' => 'Beranda',
            'label' => 'Beranda',
            'icon'  => 'bi-house-door',
        ],
        [
            'href'  => BASE_URL . 'guru/data_nilai_siswa.php',
            'page'  => 'data_nilai_siswa.php',
            'title' => 'Kelola Nilai Siswa',
            'label' => 'Kelola Nilai Siswa',
            'icon'  => 'bi-pencil-square',
        ],
    ];
    $topbar_navigation_items = $role_navigation_items;
} elseif ($current_role === 'admin') {
    // Topbar admin hanya menampilkan tautan Beranda, sesuai perilaku sebelumnya.
    $topbar_navigation_items = [
        [
            'href'  => BASE_URL . 'admin/dashboard_admin.php',
            'page'  => 'dashboard_admin.php',
            'title' => 'Beranda',
            'label' => 'Beranda',
            'icon'  => 'bi-house-door',
        ],
    ];
}

$role_label_map = [
    'admin' => 'Administrator',
    'guru'  => 'Guru',
    'siswa' => 'Siswa',
];
$current_role_label = $role_label_map[$current_role] ?? 'Tamu';
