/** Filter kata kunci, status, dan mata pelajaran pada tabel nilai guru. */
export function initGradeTableFilter() {
    const table = document.getElementById('gradeTable');
    if (!table) return;
    const searchInput = document.getElementById('gradeSearch');
    const statusFilter = document.getElementById('gradeStatusFilter');
    const subjectFilter = document.getElementById('gradeSubjectFilter');
    const resetButton = document.getElementById('gradeFilterReset');
    const resultCount = document.getElementById('gradeResultCount');
    const noResults = document.getElementById('gradeNoResults');
    const rows = Array.from(table.querySelectorAll('tbody tr[data-grade-row]'));
    const totalRows = rows.length;

    const applyFilters = () => {
        const keyword = (searchInput?.value || '').trim().toLocaleLowerCase('id');
        const status = (statusFilter?.value || '').toLocaleLowerCase('id');
        const subject = subjectFilter?.value || '';
        let visibleRows = 0;
        rows.forEach((row) => {
            const searchText = (row.dataset.search || '').toLocaleLowerCase('id');
            const rowStatus = (row.dataset.status || '').toLocaleLowerCase('id');
            const rowSubject = row.dataset.mapel || '';
            const matches = (!keyword || searchText.includes(keyword))
                && (!status || rowStatus === status)
                && (!subject || rowSubject === subject);
            row.hidden = !matches;
            if (matches) visibleRows += 1;
        });
        if (noResults) noResults.hidden = totalRows === 0 || visibleRows > 0;
        if (resultCount) resultCount.textContent = `Menampilkan ${visibleRows.toLocaleString('id-ID')} dari ${totalRows.toLocaleString('id-ID')} data`;
    };

    searchInput?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
    subjectFilter?.addEventListener('change', applyFilters);
    resetButton?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        if (subjectFilter) subjectFilter.value = '';
        applyFilters();
        searchInput?.focus();
    });
    applyFilters();
}
