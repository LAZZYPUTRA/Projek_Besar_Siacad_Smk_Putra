/** Peralihan dari modal pilihan profil ke modal edit data/foto. */
import { switchSipnModal } from './modal-controller.js';

export function initProfileModalSwitch() {
    const activate = (trigger) => {
        const selector = trigger.dataset.openModal;
        const sourceModal = document.getElementById('modalOpsiEdit');
        let targetModal = null;
        try {
            targetModal = selector ? document.querySelector(selector) : null;
        } catch (error) {
            targetModal = null;
        }
        if (!sourceModal || !targetModal?.classList.contains('modal-overlay')) return;
        switchSipnModal(sourceModal, targetModal, trigger);
    };

    document.querySelectorAll('[data-profile-modal-switch]').forEach((trigger) => {
        trigger.addEventListener('click', () => activate(trigger));
        trigger.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            activate(trigger);
        });
    });
}
