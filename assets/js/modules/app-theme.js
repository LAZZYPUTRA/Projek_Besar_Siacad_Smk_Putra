/** Mengelola tema terang/gelap SIPN dan menyimpan preferensi pengguna. */
const THEME_STORAGE_KEY = 'sipn-theme';
const VALID_THEMES = new Set(['light', 'dark']);

function readSavedTheme() {
    try {
        const savedTheme = window.localStorage.getItem(THEME_STORAGE_KEY);
        return VALID_THEMES.has(savedTheme) ? savedTheme : 'light';
    } catch (error) {
        // Aplikasi tetap berjalan bila penyimpanan browser dinonaktifkan.
        return 'light';
    }
}

function applyTheme(theme) {
    const nextTheme = VALID_THEMES.has(theme) ? theme : 'light';
    const isDark = nextTheme === 'dark';
    const root = document.documentElement;
    const body = document.body;

    root.dataset.theme = nextTheme;
    if (body) body.dataset.theme = nextTheme;

    const toggle = document.getElementById('themeToggleSidebar');
    if (toggle) {
        const icon = toggle.querySelector('i');
        const label = document.getElementById('themeText');

        toggle.setAttribute('aria-pressed', String(isDark));
        toggle.setAttribute('aria-label', isDark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap');
        if (label) label.textContent = isDark ? 'Light Mode' : 'Dark Mode';
        if (icon) {
            icon.classList.toggle('bi-moon-stars-fill', !isDark);
            icon.classList.toggle('bi-sun-fill', isDark);
        }
    }

    const themeColor = document.querySelector('meta[name="theme-color"]');
    if (themeColor) themeColor.setAttribute('content', isDark ? '#0f172a' : '#1e3a8a');
}

export function initAppTheme() {
    if (!document.body) return;

    applyTheme(readSavedTheme());

    const toggle = document.getElementById('themeToggleSidebar');
    toggle?.addEventListener('click', () => {
        const nextTheme = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
        try {
            window.localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
        } catch (error) {
            // Tema tetap berubah untuk halaman aktif walau preferensi tidak dapat disimpan.
        }
    });

    // Sinkronkan tema bila preferensi berubah dari tab SIPN lain.
    window.addEventListener('storage', (event) => {
        if (event.key === THEME_STORAGE_KEY && VALID_THEMES.has(event.newValue)) {
            applyTheme(event.newValue);
        }
    });
}
