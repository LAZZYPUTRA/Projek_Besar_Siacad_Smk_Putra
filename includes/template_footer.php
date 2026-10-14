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
const themeToggle = document.getElementById('themeToggle');
const currentTheme = localStorage.getItem('theme') || 'light';

if (currentTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
}

if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        let theme = document.documentElement.getAttribute('data-theme');
        if (theme === 'dark') {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });
}
</script>
</body>
</html>