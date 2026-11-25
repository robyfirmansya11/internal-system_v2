const themeToggle = document.getElementById('theme-toggle');
function applyTheme(dark) {
    document.documentElement.classList.toggle('dark', dark);
    themeToggle.setAttribute('aria-pressed', String(dark));
}
try { applyTheme(localStorage.getItem('portal-theme') === 'dark'); } catch {}
themeToggle.addEventListener('click', () => {
    const dark = !document.documentElement.classList.contains('dark');
    applyTheme(dark);
    try { localStorage.setItem('portal-theme', dark ? 'dark' : 'light'); } catch {}
});
document.querySelectorAll('[data-password]').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.password);
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
        button.setAttribute('aria-label', visible ? button.dataset.hide : button.dataset.show);
    });
});
