/** Pemulihan animasi saat halaman dikembalikan dari back-forward cache. */
export function initPageshowAnimations() {
    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;
        const body = document.body;
        if (body?.classList.contains('sipn-dashboard-stagger')) {
            body.classList.remove('sipn-dashboard-stagger');
            void body.offsetWidth;
            body.classList.add('sipn-dashboard-stagger');
        }
        const historyPage = document.querySelector('.sipn-admin-data-page--history');
        if (!historyPage || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const animated = historyPage.querySelectorAll('.sipn-admin-data-enter, .sipn-admin-history-table tbody tr[data-admin-search-row]');
        animated.forEach((element) => { element.style.animation = 'none'; });
        void historyPage.offsetHeight;
        animated.forEach((element) => { element.style.removeProperty('animation'); });
    });
}
