// Saout — app bootstrap
// Livewire 4 bundles Alpine; we only add small vanilla helpers here.

// --- Theme (dark mode) -------------------------------------------------------
(function initTheme() {
    const stored = localStorage.getItem('saout-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (stored === 'dark' || (!stored && prefersDark)) {
        document.documentElement.classList.add('dark');
    }
})();

window.Saout = {
    toggleTheme() {
        const el = document.documentElement;
        el.classList.toggle('dark');
        localStorage.setItem('saout-theme', el.classList.contains('dark') ? 'dark' : 'light');
    },
};

// --- PWA service worker ------------------------------------------------------
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            /* offline support is progressive; ignore failures */
        });
    });
}
