</main>
        <footer class="app-footer">
            <div class="footer-container">
                <div class="footer-info">
                    <small>&copy; <?= date('Y'); ?> <strong>SIPN</strong> — SMK Sangkuriang 1 Cimahi. Hak Cipta Dilindungi.</small>
                </div>
                <div class="footer-badges">
                    <span><i class="bi bi-patch-check-fill"></i> Terakreditasi A</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Logika Toggle Dark Mode Sidebar
    const themeToggleBtn = document.getElementById('themeToggleSidebar');
    const themeText = document.getElementById('themeText');
    const bodyElement = document.body;

    const savedTheme = localStorage.getItem('sipn_theme') || 'light';
    bodyElement.setAttribute('data-theme', savedTheme);
    if (savedTheme === 'dark' && themeText) {
        themeText.textContent = 'Light Mode';
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = bodyElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            bodyElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('sipn_theme', newTheme);
            
            if (newTheme === 'dark') {
                themeText.textContent = 'Light Mode';
            } else {
                themeText.textContent = 'Dark Mode';
            }
        });
    }

    // 2. Logika Popover Profil (Setting & Logout) di Bawah Sidebar
    const profileTrigger = document.getElementById('profileTriggerBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileTrigger && profileDropdown) {
        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = profileDropdown.style.display === 'flex';
            profileDropdown.style.display = isVisible ? 'none' : 'flex';
        });

        // Tutup popup jika area luar diklik
        document.addEventListener('click', () => {
            profileDropdown.style.display = 'none';
        });
    }
});
</script>
</body>
</html>