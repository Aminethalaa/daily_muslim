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

function urlBase64ToUint8Array(base64) {
    const padding = '='.repeat((4 - (base64.length % 4)) % 4);
    const b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(b64);
    return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
}

function meta(name) {
    return document.querySelector(`meta[name=${name}]`)?.content || '';
}

window.Saout = {
    toggleTheme() {
        const el = document.documentElement;
        el.classList.toggle('dark');
        localStorage.setItem('saout-theme', el.classList.contains('dark') ? 'dark' : 'light');
    },

    async enablePush(btn) {
        const vapid = meta('vapid-key');
        if (!vapid) return alert('الإشعارات غير مُفعّلة على الخادم بعد.');
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            return alert('متصفّحك لا يدعم الإشعارات.');
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return;

        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapid),
        });

        await fetch('/push/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': meta('csrf-token') },
            body: JSON.stringify(sub.toJSON()),
        });

        if (btn) { btn.textContent = 'الإشعارات مُفعّلة ✓'; btn.disabled = true; }
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
