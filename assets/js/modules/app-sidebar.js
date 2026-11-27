/** Interaksi sidebar: rail tablet, drawer mobile, backdrop, dan tombol Escape. */
const MOBILE_QUERY = '(max-width: 768px)';
const TABLET_QUERY = '(min-width: 769px) and (max-width: 1199px)';

export function initAppSidebar() {
    const body = document.body;
    const sidebar = document.getElementById('appSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (!body || !sidebar || !toggle) return;

    const mobileQuery = window.matchMedia(MOBILE_QUERY);
    const tabletQuery = window.matchMedia(TABLET_QUERY);

    const syncAria = () => {
        const isMobile = mobileQuery.matches;
        const isTablet = tabletQuery.matches;
        const isOpen = isMobile
            ? body.classList.contains('sidebar-open')
            : isTablet && body.classList.contains('sidebar-expanded');

        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
        sidebar.setAttribute('aria-hidden', String(isMobile && !isOpen));

        if (backdrop) {
            const visible = isMobile && isOpen;
            backdrop.classList.toggle('is-visible', visible);
            backdrop.setAttribute('aria-hidden', String(!visible));
            backdrop.tabIndex = visible ? 0 : -1;
        }
    };

    const closeSidebar = ({ restoreFocus = false } = {}) => {
        body.classList.remove('sidebar-open', 'sidebar-expanded');
        syncAria();
        document.dispatchEvent(new CustomEvent('sipn:sidebar-close'));
        if (restoreFocus) toggle.focus({ preventScroll: true });
    };

    const openSidebar = () => {
        if (mobileQuery.matches) {
            body.classList.add('sidebar-open');
            syncAria();
            const firstLink = sidebar.querySelector('.sidebar-menu-container a.nav-link, .sidebar-menu-container button.nav-link');
            firstLink?.focus({ preventScroll: true });
        } else if (tabletQuery.matches) {
            body.classList.add('sidebar-expanded');
            syncAria();
        }
    };

    toggle.addEventListener('click', () => {
        const isOpen = mobileQuery.matches
            ? body.classList.contains('sidebar-open')
            : tabletQuery.matches && body.classList.contains('sidebar-expanded');
        if (isOpen) closeSidebar({ restoreFocus: true });
        else openSidebar();
    });

    backdrop?.addEventListener('click', () => closeSidebar({ restoreFocus: true }));

    sidebar.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (link && mobileQuery.matches) closeSidebar();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        if (body.classList.contains('sidebar-open') || body.classList.contains('sidebar-expanded')) {
            closeSidebar({ restoreFocus: true });
        }
    });

    const handleViewportChange = () => {
        // State mobile dan tablet tidak dibawa ke breakpoint lain.
        body.classList.remove('sidebar-open', 'sidebar-expanded');
        syncAria();
    };

    if (typeof mobileQuery.addEventListener === 'function') {
        mobileQuery.addEventListener('change', handleViewportChange);
        tabletQuery.addEventListener('change', handleViewportChange);
    } else {
        // Fallback untuk browser lama yang masih memakai addListener.
        mobileQuery.addListener(handleViewportChange);
        tabletQuery.addListener(handleViewportChange);
    }

    syncAria();
}
