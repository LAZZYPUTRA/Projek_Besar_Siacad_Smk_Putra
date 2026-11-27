/**
 * Pencarian tabel admin.
 * Kontrak HTML: [data-admin-search-table], [data-admin-table-search],
 * [data-admin-search-row], [data-admin-search-clear], [data-admin-results-count],
 * [data-admin-search-empty], dan opsional [data-search-ignore].
 */
export function initAdminTableSearch() {
    document.querySelectorAll('[data-admin-search-table]').forEach((table) => {
        const page = table.closest('.sipn-admin-data-page') || document;
        const searchInput = page.querySelector('[data-admin-table-search]');
        const clearButton = page.querySelector('[data-admin-search-clear]');
        const resultCount = page.querySelector('[data-admin-results-count]');
        const emptyState = page.querySelector('[data-admin-search-empty]');
        const rows = Array.from(table.querySelectorAll('tbody tr[data-admin-search-row]'));
        const totalRows = rows.length;
        if (!searchInput) return;

        const normalize = (value) => String(value ?? '')
            .toLocaleLowerCase('id')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
        const rowText = (row) => row.dataset.search || Array.from(row.cells)
            .filter((cell) => !cell.hasAttribute('data-search-ignore'))
            .map((cell) => cell.textContent || '')
            .join(' ');
        const indexedRows = rows.map((row) => ({ row, text: normalize(rowText(row)) }));

        const applyFilter = () => {
            const keyword = normalize(searchInput.value);
            let visibleRows = 0;
            indexedRows.forEach(({ row, text }) => {
                const matches = keyword === '' || text.includes(keyword);
                row.hidden = !matches;
                if (matches) visibleRows += 1;
            });
            if (resultCount) resultCount.textContent = visibleRows.toLocaleString('id-ID');
            if (emptyState) emptyState.hidden = totalRows === 0 || visibleRows > 0;
        };

        searchInput.addEventListener('input', applyFilter);
        clearButton?.addEventListener('click', () => {
            searchInput.value = '';
            applyFilter();
            searchInput.focus();
        });
        applyFilter();
    });
}
