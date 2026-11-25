// Share the portal's preference with the Livewire/Alpine theme switcher.
try {
    const theme = localStorage.getItem('portal-theme');
    if (theme === 'dark' || theme === 'light') {
        localStorage.setItem('theme', theme);
        window.theme = theme;
        document.documentElement.classList.toggle('dark', theme === 'dark');
    }
} catch {}
window.addEventListener('theme-changed', event => {
    const theme = typeof event.detail === 'string' ? event.detail : event.detail?.theme;
    if (['light', 'dark', 'system'].includes(theme)) {
        const preference = theme === 'system'
            ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
            : theme;
        try { localStorage.setItem('portal-theme', preference); } catch {}
    }
});
document.addEventListener('livewire:init', () => {
    Livewire.on('open-new-tab', event => {
        window.open(event.url, '_blank', 'noopener');
    });
});
