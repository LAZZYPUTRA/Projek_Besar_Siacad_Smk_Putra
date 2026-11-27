<?php
/**
 * View contact.
 * Variabel data disiapkan oleh controller contact.php.
 * File ini hanya menangani presentasi; koneksi dan otorisasi tetap pada controller.
 */
?>
<div class="page-container narrow sipn-utility-page sipn-utility-page--contact">
    <div class="contact-wrapper">
        <div class="contact-header">
            <div class="contact-header-icon">
                <i class="bi bi-headset"></i>
            </div>
            <h2 class="contact-title">Pusat Bantuan & Kontak</h2>
            <p class="contact-subtitle">Tim Layanan Teknis IT SMK Sangkuriang 1 Cimahi siap membantu Anda.</p>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <i class="bi bi-envelope-fill contact-icon email"></i>
                <h4 class="contact-card-title">Email Resmi</h4>
                <!-- Tautan Email Interaktif -->
                <a href="mailto:support@smksangkuriang1.sch.id" class="contact-link">support@smksangkuriang1.sch.id</a>
            </div>

            <div class="contact-card">
                <i class="bi bi-whatsapp contact-icon whatsapp"></i>
                <h4 class="contact-card-title">Helpdesk WhatsApp</h4>
                <!-- Tautan WhatsApp Interaktif (Membuka tab baru) -->
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="contact-link">+62 812-3456-7890 (Jam Kerja)</a>
            </div>
        </div>

        <div class="contact-location">
            <h4 class="contact-location-title">Lokasi Unit IT Sekolah</h4>
            <p class="contact-location-text">Gedung Pusat Komputer & Jaringan Lantai 2, SMK Sangkuriang 1 Cimahi, Jl. Sangkuriang No. 39 Cimahi.</p>
        </div>
    </div>
</div>
