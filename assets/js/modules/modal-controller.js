/** Controller modal global SIPN: buka, tutup, Escape, backdrop, dan focus trap. */
const openerByModal = new WeakMap();
let previousBodyOverflow = '';
let bodyLockActive = false;

const FOCUSABLE_SELECTOR = [
    'a[href]',
    'area[href]',
    'button:not([disabled])',
    'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

function getFocusableElements(modal) {
    return Array.from(modal.querySelectorAll(FOCUSABLE_SELECTOR)).filter((element) => {
        return !element.hidden && element.getAttribute('aria-hidden') !== 'true'
            && element.getClientRects().length > 0;
    });
}

function syncBodyLock() {
    const hasOpenModal = Boolean(document.querySelector('.modal-overlay.is-active'));
    if (hasOpenModal && !bodyLockActive) {
        previousBodyOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        bodyLockActive = true;
    } else if (!hasOpenModal && bodyLockActive) {
        document.body.style.overflow = previousBodyOverflow;
        bodyLockActive = false;
    }
}

export function openSipnModal(modal, opener = document.activeElement) {
    if (!(modal instanceof HTMLElement) || !modal.classList.contains('modal-overlay')) return false;

    document.querySelectorAll('.modal-overlay.is-active').forEach((activeModal) => {
        if (activeModal === modal) return;
        activeModal.classList.remove('is-active');
        activeModal.setAttribute('aria-hidden', 'true');
    });

    if (opener instanceof HTMLElement) openerByModal.set(modal, opener);
    modal.classList.add('is-active');
    modal.setAttribute('aria-hidden', 'false');

    const dialog = modal.querySelector('.modal-box');
    if (dialog) {
        if (!dialog.hasAttribute('role')) dialog.setAttribute('role', 'dialog');
        if (!dialog.hasAttribute('tabindex')) dialog.setAttribute('tabindex', '-1');
        dialog.setAttribute('aria-modal', 'true');
    }

    syncBodyLock();
    window.requestAnimationFrame(() => {
        if (!modal.classList.contains('is-active')) return;
        const focusable = getFocusableElements(modal);
        if (focusable.length) focusable[0].focus({ preventScroll: true });
        else dialog?.focus({ preventScroll: true });
    });
    return true;
}

export function closeSipnModal(modal, { restoreFocus = true } = {}) {
    if (!(modal instanceof HTMLElement) || !modal.classList.contains('modal-overlay')) return false;

    modal.classList.remove('is-active');
    modal.setAttribute('aria-hidden', 'true');
    const opener = openerByModal.get(modal);
    openerByModal.delete(modal);
    syncBodyLock();

    if (restoreFocus && opener?.isConnected) {
        window.requestAnimationFrame(() => opener.focus({ preventScroll: true }));
    }
    return true;
}

export function switchSipnModal(sourceModal, targetModal, trigger) {
    if (!sourceModal || !targetModal) return false;
    const originalOpener = openerByModal.get(sourceModal) || trigger || document.activeElement;
    closeSipnModal(sourceModal, { restoreFocus: false });
    return openSipnModal(targetModal, originalOpener);
}

export function initModalController() {
    // Tetapkan keadaan aksesibilitas awal bagi semua modal pada halaman.
    document.querySelectorAll('.modal-overlay').forEach((modal) => {
        if (!modal.classList.contains('is-active')) modal.setAttribute('aria-hidden', 'true');
    });

    document.addEventListener('click', (event) => {
        const clickTarget = event.target instanceof Element ? event.target : null;
        if (!clickTarget) return;

        const openTrigger = clickTarget.closest('[data-modal-target]');
        if (openTrigger) {
            const selector = openTrigger.getAttribute('data-modal-target');
            let modal = null;
            try {
                modal = selector ? document.querySelector(selector) : null;
            } catch (error) {
                modal = null;
            }
            if (modal?.classList.contains('modal-overlay')) {
                event.preventDefault();
                openSipnModal(modal, openTrigger);
                return;
            }
        }

        const closeTrigger = clickTarget.closest('[data-modal-close]');
        if (closeTrigger) {
            const modal = closeTrigger.closest('.modal-overlay');
            if (modal) {
                event.preventDefault();
                closeSipnModal(modal);
            }
            return;
        }

        if (clickTarget.classList.contains('modal-overlay')) {
            closeSipnModal(clickTarget);
        }
    });

    document.addEventListener('keydown', (event) => {
        const activeModal = document.querySelector('.modal-overlay.is-active');
        if (!activeModal) return;

        if (event.key === 'Escape') {
            event.preventDefault();
            closeSipnModal(activeModal);
            return;
        }

        if (event.key !== 'Tab') return;
        const focusable = getFocusableElements(activeModal);
        if (!focusable.length) {
            event.preventDefault();
            activeModal.querySelector('.modal-box')?.focus();
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (!activeModal.contains(document.activeElement)) {
            event.preventDefault();
            first.focus();
        } else if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
}
