<?php
require_once __DIR__ . '/config/koneksi_database.php';
require_once __DIR__ . '/includes/helper_fungsi.php';
require_once __DIR__ . '/includes/template_header.php';
?>

<div class="page-container" style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 70px; height: 70px; background: rgba(37, 99, 235, 0.1); color: var(--accent-orange); border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
                <i class="bi bi-headset"></i>
            </div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark);">Pusat Bantuan & Kontak</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.3rem;">Tim Layanan Teknis IT SMK Sangkuriang 1 Cimahi siap membantu Anda.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 14px; text-align: center;">
                <i class="bi bi-envelope-fill" style="font-size: 1.5rem; color: var(--primary-dark); margin-bottom: 0.5rem; display: block;"></i>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem;">Email Resmi</h4>
                <p style="font-size: 0.82rem; color: var(--text-muted);">support@smksangkuriang1.sch.id</p>
            </div>

            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 14px; text-align: center;">
                <i class="bi bi-whatsapp" style="font-size: 1.5rem; color: #10b981; margin-bottom: 0.5rem; display: block;"></i>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem;">Helpdesk WhatsApp</h4>
                <p style="font-size: 0.82rem; color: var(--text-muted);">+62 812-3456-7890 (Jam Kerja)</p>
            </div>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 14px; text-align: center;">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem;">Lokasi Unit IT Sekolah</h4>
            <p style="font-size: 0.85rem; color: var(--text-muted);">Gedung Pusat Komputer & Jaringan Lantai 2, SMK Sangkuriang 1 Cimahi, Jl. Sangkuriang No. 39 Cimahi.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/template_footer.php'; ?>