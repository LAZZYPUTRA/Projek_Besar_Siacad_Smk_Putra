</main> <!-- Akhir Container Utama -->

<footer class="footer mt-auto py-3 bg-white border-top text-center text-muted shadow-sm">
    <div class="container">
        <small>&copy; <?= date('Y'); ?> <strong>SIPN</strong> - SMK Sangkuriang 1 Cimahi. All rights reserved.</small>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script otomatis menandai menu aktif di Navbar
    document.addEventListener("DOMContentLoaded", function() {
        let currentUrl = window.location.href;
        let navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(function(link) {
            // Jika URL saat ini cocok dengan href menu
            if (link.href === currentUrl) {
                link.classList.add('active');
                link.classList.add('fw-bold'); // Menebalkan teks
            }
        });
    });
</script>
</body>
</html>