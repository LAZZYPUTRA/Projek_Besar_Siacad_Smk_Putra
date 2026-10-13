document.addEventListener('DOMContentLoaded', () => {
    // Handling Dropdown User Nav
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        userMenu.addEventListener('click', (e) => {
            userMenu.classList.toggle('is-open');
            e.stopPropagation();
        });
        document.addEventListener('click', () => userMenu.classList.remove('is-open'));
    }

    // Handling Native Modal (Open)
    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = trigger.getAttribute('data-modal-target');
            const modal = document.querySelector(targetId);
            if (modal) {
                modal.classList.add('is-active');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    // Handling Native Modal (Close)
    document.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
        closeBtn.addEventListener('click', () => {
            const modal = closeBtn.closest('.modal-overlay');
            if (modal) {
                modal.classList.remove('is-active');
                document.body.style.overflow = '';
            }
        });
    });

    // Close Modal when clicking outer backdrop
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('is-active');
                document.body.style.overflow = '';
            }
        });
    });
});