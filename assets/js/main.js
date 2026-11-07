/* SIPN — Global UI interactions */
(() => {
    'use strict';

    document.addEventListener('DOMContentLoaded', initializeSIPNUI);

    function initializeSIPNUI() {
        const body = document.body;

        initializeThemeToggle();
        initializeProfileDropdown();
        initializeModalController();
        initializeResponsiveSidebar();

        function initializeThemeToggle() {
            const themeToggle = document.getElementById('themeToggleSidebar');
            const themeText = document.getElementById('themeText');
            let savedTheme = 'light';

            try {
                const storedTheme = window.localStorage.getItem('sipn_theme');
                if (storedTheme === 'dark' || storedTheme === 'light') {
                    savedTheme = storedTheme;
                }
            } catch (error) {
                console.warn('Preferensi tema tidak dapat dibaca.', error);
            }

            const applyTheme = (theme) => {
                body.setAttribute('data-theme', theme);

                if (themeText) {
                    themeText.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
                }

                if (themeToggle) {
                    themeToggle.setAttribute('aria-pressed', String(theme === 'dark'));
                    themeToggle.setAttribute(
                        'aria-label',
                        theme === 'dark' ? 'Beralih ke tema terang' : 'Beralih ke tema gelap'
                    );
                }
            };

            applyTheme(savedTheme);

            if (!themeToggle) return;

            themeToggle.addEventListener('click', () => {
                const currentTheme = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
                const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(nextTheme);

                try {
                    window.localStorage.setItem('sipn_theme', nextTheme);
                } catch (error) {
                    console.warn('Preferensi tema tidak dapat disimpan.', error);
                }
            });
        }

        function initializeProfileDropdown() {
            const trigger = document.getElementById('profileTriggerBtn');
            const dropdown = document.getElementById('profileDropdown');
            if (!trigger || !dropdown) return;

            const setOpen = (isOpen) => {
                dropdown.classList.toggle('is-open', isOpen);
                dropdown.setAttribute('aria-hidden', String(!isOpen));
                trigger.setAttribute('aria-expanded', String(isOpen));
            };

            setOpen(false);

            trigger.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = trigger.getAttribute('aria-expanded') === 'true';
                setOpen(!isOpen);
            });

            dropdown.addEventListener('click', (event) => event.stopPropagation());

            document.addEventListener('click', (event) => {
                if (!trigger.contains(event.target) && !dropdown.contains(event.target)) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
                    setOpen(false);
                    trigger.focus();
                }
            });
        }

        function initializeModalController() {
            let activeModal = null;
            let returnFocusTo = null;
            let previousBodyOverflow = '';

            const getFocusableElements = (container) => {
                if (!container) return [];

                const selector = [
                    'a[href]',
                    'area[href]',
                    'button:not([disabled])',
                    'input:not([disabled]):not([type="hidden"])',
                    'select:not([disabled])',
                    'textarea:not([disabled])',
                    'iframe',
                    '[tabindex]:not([tabindex="-1"])'
                ].join(',');

                return Array.from(container.querySelectorAll(selector)).filter((element) => {
                    return !element.hasAttribute('hidden') && element.getAttribute('aria-hidden') !== 'true';
                });
            };

            const openModal = (selector, trigger) => {
                let modal;
                try {
                    modal = document.querySelector(selector);
                } catch (error) {
                    console.error('Selector modal tidak valid:', selector, error);
                    return;
                }

                if (!modal || !modal.classList.contains('modal-overlay')) {
                    console.warn('Modal target tidak ditemukan:', selector);
                    return;
                }

                if (activeModal && activeModal !== modal) {
                    closeModal(activeModal, false);
                }

                returnFocusTo = trigger || document.activeElement;
                previousBodyOverflow = body.style.overflow;
                activeModal = modal;

                modal.classList.add('is-active');
                modal.setAttribute('role', 'dialog');
                modal.setAttribute('aria-modal', 'true');
                modal.setAttribute('aria-hidden', 'false');
                if (!modal.hasAttribute('tabindex')) modal.setAttribute('tabindex', '-1');

                body.style.overflow = 'hidden';

                const focusable = getFocusableElements(modal);
                (focusable[0] || modal).focus();
            };

            const closeModal = (modal = activeModal, restoreFocus = true) => {
                if (!modal) return;

                modal.classList.remove('is-active');
                modal.setAttribute('aria-hidden', 'true');
                modal.removeAttribute('aria-modal');

                if (modal === activeModal) {
                    activeModal = null;
                    body.style.overflow = previousBodyOverflow;
                }

                if (restoreFocus && returnFocusTo && document.contains(returnFocusTo)) {
                    returnFocusTo.focus();
                }
                if (modal === activeModal || !activeModal) returnFocusTo = null;
            };

            document.addEventListener('click', (event) => {
                const target = event.target instanceof Element ? event.target : event.target.parentElement;
                if (!target) return;

                const opener = target.closest('[data-modal-target]');
                if (opener) {
                    event.preventDefault();
                    openModal(opener.getAttribute('data-modal-target'), opener);
                    return;
                }

                const closer = target.closest('[data-modal-close]');
                if (closer) {
                    event.preventDefault();
                    closeModal(closer.closest('.modal-overlay'));
                    return;
                }

                if (activeModal && target === activeModal) {
                    closeModal(activeModal);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (!activeModal) return;

                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeModal(activeModal);
                    return;
                }

                if (event.key !== 'Tab') return;

                const focusable = getFocusableElements(activeModal);
                if (focusable.length === 0) {
                    event.preventDefault();
                    activeModal.focus();
                    return;
                }

                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && (document.activeElement === first || !activeModal.contains(document.activeElement))) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && (document.activeElement === last || !activeModal.contains(document.activeElement))) {
                    event.preventDefault();
                    first.focus();
                }
            });
        }

        function initializeResponsiveSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const toggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            const mainContent = document.getElementById('appMainContent');

            if (!sidebar || !toggle || !backdrop || !mainContent) return;

            const mobileQuery = window.matchMedia('(max-width: 768px)');
            const tabletQuery = window.matchMedia('(min-width: 769px) and (max-width: 1199px)');

            const setToggleState = (expanded, label) => {
                toggle.setAttribute('aria-expanded', String(expanded));
                toggle.setAttribute('aria-label', label);
            };

            const closeMobileSidebar = (restoreFocus = false) => {
                body.classList.remove('sidebar-open');
                backdrop.classList.remove('is-visible');
                backdrop.setAttribute('aria-hidden', 'true');
                backdrop.tabIndex = -1;
                sidebar.setAttribute('aria-hidden', 'true');
                sidebar.inert = true;
                mainContent.inert = false;
                setToggleState(false, 'Buka menu navigasi');
                if (restoreFocus) toggle.focus();
            };

            const openMobileSidebar = () => {
                body.classList.add('sidebar-open');
                backdrop.classList.add('is-visible');
                backdrop.setAttribute('aria-hidden', 'false');
                backdrop.tabIndex = 0;
                sidebar.setAttribute('aria-hidden', 'false');
                sidebar.inert = false;
                mainContent.inert = true;
                setToggleState(true, 'Tutup menu navigasi');

                const firstLink = sidebar.querySelector('a.nav-link, button.nav-link');
                if (firstLink) firstLink.focus();
            };

            const syncViewportState = () => {
                if (mobileQuery.matches) {
                    body.classList.remove('sidebar-expanded');
                    const isOpen = body.classList.contains('sidebar-open');
                    sidebar.inert = !isOpen;
                    sidebar.setAttribute('aria-hidden', String(!isOpen));
                    mainContent.inert = isOpen;
                    backdrop.classList.toggle('is-visible', isOpen);
                    backdrop.setAttribute('aria-hidden', String(!isOpen));
                    backdrop.tabIndex = isOpen ? 0 : -1;
                    setToggleState(isOpen, isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
                    return;
                }

                body.classList.remove('sidebar-open');
                backdrop.classList.remove('is-visible');
                backdrop.setAttribute('aria-hidden', 'true');
                backdrop.tabIndex = -1;
                sidebar.inert = false;
                sidebar.setAttribute('aria-hidden', 'false');
                mainContent.inert = false;

                if (tabletQuery.matches) {
                    const isExpanded = body.classList.contains('sidebar-expanded');
                    setToggleState(isExpanded, isExpanded ? 'Ciutkan navigasi' : 'Perluas navigasi');
                } else {
                    body.classList.remove('sidebar-expanded');
                    setToggleState(false, 'Navigasi utama');
                }
            };

            toggle.addEventListener('click', () => {
                if (mobileQuery.matches) {
                    const isOpen = body.classList.contains('sidebar-open');
                    if (isOpen) closeMobileSidebar(true);
                    else openMobileSidebar();
                    return;
                }

                if (tabletQuery.matches) {
                    const isExpanded = body.classList.toggle('sidebar-expanded');
                    setToggleState(isExpanded, isExpanded ? 'Ciutkan navigasi' : 'Perluas navigasi');
                }
            });

            backdrop.addEventListener('click', () => closeMobileSidebar(true));

            sidebar.addEventListener('click', (event) => {
                const link = event.target instanceof Element ? event.target.closest('a.nav-link') : null;
                if (link && mobileQuery.matches) closeMobileSidebar(false);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    if (mobileQuery.matches && body.classList.contains('sidebar-open')) {
                        closeMobileSidebar(true);
                    } else if (tabletQuery.matches && body.classList.contains('sidebar-expanded')) {
                        body.classList.remove('sidebar-expanded');
                        setToggleState(false, 'Perluas navigasi');
                        toggle.focus();
                    }
                    return;
                }

                if (event.key !== 'Tab' || !mobileQuery.matches || !body.classList.contains('sidebar-open')) return;

                const focusable = Array.from(sidebar.querySelectorAll(
                    'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                )).filter((element) => !element.hasAttribute('hidden'));

                if (focusable.length === 0) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && (document.activeElement === first || !sidebar.contains(document.activeElement))) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && (document.activeElement === last || !sidebar.contains(document.activeElement))) {
                    event.preventDefault();
                    first.focus();
                }
            });

            if (typeof mobileQuery.addEventListener === 'function') {
                mobileQuery.addEventListener('change', syncViewportState);
                tabletQuery.addEventListener('change', syncViewportState);
            } else {
                mobileQuery.addListener(syncViewportState);
                tabletQuery.addListener(syncViewportState);
            }

            syncViewportState();
        }
    }
})();