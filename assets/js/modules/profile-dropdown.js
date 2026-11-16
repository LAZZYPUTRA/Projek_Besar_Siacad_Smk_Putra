/** Dropdown menu profil pada bagian bawah sidebar. */
export function initProfileDropdown() {
    const trigger = document.getElementById('profileTriggerBtn');
    const dropdown = document.getElementById('profileDropdown');
    if (!trigger || !dropdown) return;

    const setOpen = (open, { restoreFocus = false } = {}) => {
        dropdown.classList.toggle('is-open', open);
        dropdown.setAttribute('aria-hidden', String(!open));
        trigger.setAttribute('aria-expanded', String(open));
        if (restoreFocus) trigger.focus({ preventScroll: true });
    };

    // Pastikan status awal sesuai markup/CSS.
    setOpen(false);

    trigger.addEventListener('click', () => {
        setOpen(!dropdown.classList.contains('is-open'));
    });

    document.addEventListener('pointerdown', (event) => {
        if (!dropdown.classList.contains('is-open')) return;
        if (trigger.contains(event.target) || dropdown.contains(event.target)) return;
        setOpen(false);
    });

    dropdown.addEventListener('click', (event) => {
        if (event.target.closest('a')) setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && dropdown.classList.contains('is-open')) {
            setOpen(false, { restoreFocus: true });
        }
    });

    document.addEventListener('sipn:sidebar-close', () => setOpen(false));
}
