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
    // ==========================================
    // 1. UNIVERSAL MODAL HANDLER (GLOBAL)
    // ==========================================
    document.addEventListener('click', function(e) {
        // Ketika tombol dengan atribut data-modal-target diklik
        const trigger = e.target.closest('[data-modal-target]');
        if (trigger) {
            e.preventDefault();
            const targetSelector = trigger.getAttribute('data-modal-target');
            const modal = document.querySelector(targetSelector);
            if (modal) {
                modal.classList.add('is-active');
                modal.style.display = 'flex';
            } else {
                console.warn('Modal target tidak ditemukan:', targetSelector);
            }
        }

        // Ketika tombol close / batal di dalam modal diklik
        const closer = e.target.closest('[data-modal-close]');
        if (closer) {
            e.preventDefault();
            const modal = closer.closest('.modal-overlay');
            if (modal) {
                modal.classList.remove('is-active');
                modal.style.display = 'none';
            }
        }

        // Ketika area gelap di luar kotak modal diklik
        if (e.target.classList.contains('modal-overlay')) {
            e.target.classList.remove('is-active');
            e.target.style.display = 'none';
        }
    });

    // ==========================================
    // 2. TOGGLE DARK MODE SIDEBAR
    // ==========================================
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

    // ==========================================
    // 3. POPOVER PROFIL (SETTING & LOGOUT)
    // ==========================================
    const profileTrigger = document.getElementById('profileTriggerBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileTrigger && profileDropdown) {
        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = profileDropdown.style.display === 'flex';
            profileDropdown.style.display = isVisible ? 'none' : 'flex';
        });

        document.addEventListener('click', () => {
            profileDropdown.style.display = 'none';
        });
    }
});
</script>
</body>
</html>