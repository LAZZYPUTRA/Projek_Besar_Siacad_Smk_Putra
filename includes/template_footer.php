</main>

<footer class="app-footer">
    <div class="footer-container">
        <div class="footer-info">
            <small>&copy; <?= date('Y'); ?> <strong>SIPN</strong> — SMK Sangkuriang 1 Cimahi. Hak Cipta Dilindungi.</small>
        </div>
        <div class="footer-badges">
            <span><i class="bi bi-patch-check-fill"></i> Terakreditasi A</span>
            <span><i class="bi bi-shield-lock-fill"></i> SSL 256-Bit</span>
        </div>
    </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Menandai menu aktif berdasarkan URL
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        if (currentUrl.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });

    // Toggle dropdown profil pengguna
    const userMenu = document.getElementById('userMenu');
    if (userMenu) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('is-open');
        });

        document.addEventListener('click', function() {
            userMenu.classList.remove('is-open');
        });
    }
});
</script>
</body>
</html>