/** Pratinjau foto profil sebelum formulir diunggah. */
export function initProfilePhotoPreview() {
    const input = document.querySelector('[data-profile-photo-input]');
    if (!input) return;
    const initialContent = document.getElementById('dropzoneContentAwal');
    const previewWrapper = document.getElementById('wadahPreview');
    const previewImage = document.getElementById('gambarPreview');
    const fileBadge = document.getElementById('badgeNamaFile');
    const fileName = document.getElementById('teksNamaFile');
    if (!initialContent || !previewWrapper || !previewImage || !fileBadge || !fileName) return;

    const resetPreview = () => {
        initialContent.style.display = 'block';
        previewWrapper.style.display = 'none';
        fileBadge.style.display = 'none';
        previewImage.removeAttribute('src');
        fileName.textContent = '-';
    };

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) { resetPreview(); return; }
        if (!file.type.startsWith('image/')) {
            input.value = '';
            resetPreview();
            return;
        }
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            if (typeof reader.result !== 'string') return;
            initialContent.style.display = 'none';
            previewWrapper.style.display = 'block';
            previewImage.src = reader.result;
            fileBadge.style.display = 'inline-flex';
            fileName.textContent = file.name;
        });
        reader.addEventListener('error', () => { input.value = ''; resetPreview(); });
        reader.readAsDataURL(file);
    });
}
