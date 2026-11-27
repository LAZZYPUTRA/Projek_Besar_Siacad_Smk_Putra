/** SIPN JavaScript entry point. Semua fitur global dan halaman diinisialisasi di sini. */
import { initAppTheme } from './modules/app-theme.js';
import { initAppSidebar } from './modules/app-sidebar.js';
import { initProfileDropdown } from './modules/profile-dropdown.js';
import { initModalController } from './modules/modal-controller.js';
import { initAdminTableSearch } from './modules/admin-table-search.js';
import { initGradeTableFilter } from './modules/grade-table-filter.js';
import { initProfilePhotoPreview } from './modules/profile-photo-preview.js';
import { initProfileModalSwitch } from './modules/profile-modal-switch.js';
import { initPrintActions } from './modules/print-actions.js';
import { initPageshowAnimations } from './modules/pageshow-animations.js';

function initializeSipnScripts() {
    initAppTheme();
    initAppSidebar();
    initProfileDropdown();
    initModalController();
    initAdminTableSearch();
    initGradeTableFilter();
    initProfilePhotoPreview();
    initProfileModalSwitch();
    initPrintActions();
    initPageshowAnimations();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSipnScripts, { once: true });
} else {
    initializeSipnScripts();
}
