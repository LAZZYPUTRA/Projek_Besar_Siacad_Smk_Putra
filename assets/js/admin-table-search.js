/* SIPN — Pencarian langsung untuk direktori guru dan siswa admin. */
(() => {
    'use strict';

    function initializeAdminTableSearch() {
        const page = document.querySelector('.sipn-admin-data-page');
        if (!page) return;

        const input = page.querySelector('[data-admin-table-search]');
        const table = page.querySelector('[data-admin-search-table]');
        const clearButton = page.querySelector('[data-admin-search-clear]');
        const resultCount = page.querySelector('[data-admin-results-count]');
        const noMatch = page.querySelector('[data-admin-search-empty]');

        if (!input || !table) return;

        const rows = Array.from(table.querySelectorAll('tbody tr[data-admin-search-row]'));
        const normalize = (value) => String(value || '')
            .toLocaleLowerCase('id')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();

        const applySearch = () => {
            const keyword = normalize(input.value);
            let visible = 0;

            rows.forEach((row) => {
                const searchableCells = Array.from(row.querySelectorAll('td:not([data-search-ignore])'));
                const haystack = normalize(searchableCells.map((cell) => cell.textContent).join(' '));
                const matches = keyword === '' || haystack.includes(keyword);
                row.hidden = !matches;
                if (matches) visible += 1;
            });

            if (resultCount) resultCount.textContent = String(visible);
            if (noMatch) noMatch.hidden = visible > 0 || rows.length === 0;
        };

        input.addEventListener('input', applySearch);

        if (clearButton) {
            clearButton.addEventListener('click', () => {
                input.value = '';
                applySearch();
                input.focus();
            });
        }

        applySearch();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAdminTableSearch, { once: true });
    } else {
        initializeAdminTableSearch();
    }
})();