const themeButton = document.getElementById('theme-toggle');
function setTheme(dark) {
    document.documentElement.classList.toggle('dark', dark);
    themeButton.setAttribute('aria-pressed', String(dark));
}
try { setTheme(localStorage.getItem('portal-theme') === 'dark'); } catch {}
themeButton.addEventListener('click', () => {
    const dark = !document.documentElement.classList.contains('dark');
    setTheme(dark);
    try { localStorage.setItem('portal-theme', dark ? 'dark' : 'light'); } catch {}
});
const menu = document.getElementById('menu-toggle');
menu.addEventListener('click', () => {
    const open = document.body.classList.toggle('menu-open');
    menu.setAttribute('aria-expanded', String(open));
});
document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        document.body.classList.remove('menu-open');
        menu.setAttribute('aria-expanded', 'false');
    }
});
document.getElementById('module-search').addEventListener('input', event => {
    let visible = 0;
    document.querySelectorAll('[data-module]').forEach(module => {
        module.hidden = !module.dataset.module.includes(event.target.value.trim().toLowerCase());
        if (!module.hidden) visible++;
    });
    document.getElementById('search-empty').hidden = visible > 0;
});
