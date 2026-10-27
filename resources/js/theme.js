const STORAGE_KEY = 'finsight-theme';

function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY);
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.style.colorScheme = theme;
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
}

function toggleTheme() {
    const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
}

function initTheme() {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
        if (!getStoredTheme()) {
            applyTheme(event.matches ? 'dark' : 'light');
        }
    });
}

window.FinSightTheme = {
    STORAGE_KEY,
    apply: applyTheme,
    toggle: toggleTheme,
    get: () => (document.documentElement.classList.contains('dark') ? 'dark' : 'light'),
    init: initTheme,
};

initTheme();
