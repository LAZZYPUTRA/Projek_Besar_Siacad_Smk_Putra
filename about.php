<?php
require_once __DIR__ . '/config/koneksi_database.php';
require_once __DIR__ . '/includes/helper_fungsi.php';
require_once __DIR__ . '/includes/template_header.php';
?>

<div class="page-container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 70px; height: 70px; background: rgba(30, 58, 138, 0.1); color: var(--primary-dark); border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark);">Tentang SIPN</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.3rem;">Sistem Informasi Pengelolaan Nilai Terpadu • SMK Sangkuriang 1 Cimahi</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.25rem; color: var(--text-dark); line-height: 1.6; font-size: 0.92rem;">
            <p><strong>SIPN</strong> adalah platform digital resmi yang dikembangkan untuk mendukung proses evaluasi akademik, rekapitulasi nilai capaian kompetensi peserta didik, serta transparansi transkrip rapor berbasis Kurikulum Merdeka di lingkungan SMK Sangkuriang 1 Cimahi.</p>
            
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 12px; margin: 1rem 0;">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--primary-dark);">Fitur Utama Sistem:</h4>
                <ul style="padding-left: 1.25rem; display: flex; flex-direction: column; gap: 0.4rem; color: var(--text-muted);">
                    <li>Manajemen data induk peserta didik dan pendidik yang terintegrasi.</li>
                    <li>Penginputan nilai pengetahuan dan keterampilan secara akurat oleh guru pengampu.</li>
                    <li>Transkrip nilai rapor real-time yang dapat diakses dan dicetak oleh siswa kapan saja.</li>
                    <li>Keamanan data berbasis enkripsi standar enterprise dan dukungan <em>Dark Mode</em>.</li>
                </ul>
            </div>

            <p style="text-align: center; font-size: 0.82rem; color: var(--text-muted); margin-top: 1rem;">
                &copy; 2026 Tim IT & Pengembangan Akademik SMK Sangkuriang 1 Cimahi. Hak Cipta Dilindungi.
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/template_footer.php'; ?>