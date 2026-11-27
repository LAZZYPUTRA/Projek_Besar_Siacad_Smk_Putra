/**
 * Entry point kompatibilitas SIPN.
 * File ini mempertahankan jalur assets/js/main.js bagi template lama.
 * Implementasi aktual berada pada sipn-modules.js dan modules/.
 */
(() => {
    if (window.__sipnMainJsBridgeLoaded) return;
    window.__sipnMainJsBridgeLoaded = true;

    import('./sipn-modules.js').catch((error) => {
        console.error('SIPN: modul JavaScript gagal dimuat.', error);
    });
})();
