import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const themeToggleButtons = document.querySelectorAll('[data-theme-toggle]');

const updateThemeToggleButtons = (isDark) => {
    themeToggleButtons.forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
        button.setAttribute('aria-label', isDark ? 'Attiva modalità chiara' : 'Attiva modalità scura');
        button.setAttribute('title', isDark ? 'Attiva modalità chiara' : 'Attiva modalità scura');
    });
};

updateThemeToggleButtons(document.documentElement.classList.contains('dark'));

themeToggleButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateThemeToggleButtons(isDark);
    });
});
