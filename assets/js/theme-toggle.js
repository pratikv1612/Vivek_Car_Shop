/* ==========================================================
   V AutoSpare - light / dark / system theme toggle
   Extracted from application/views/layout/footer.php
   ========================================================== */
(function() {
    const themeKey = 'vautospareTheme';
    const themeMeta = document.querySelector('meta[name="theme-color"]');
    const themeSwitcherBtn = document.getElementById('themeSwitcherBtn');
    const themeOptions = document.querySelectorAll('.theme-option');
    const labels = { system: 'System Default', light: 'Light Mode', dark: 'Dark Mode' };
    const icons = { system: 'bi-circle-half', light: 'bi-brightness-high', dark: 'bi-moon-stars' };

    const getPreferred = () => window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

    const applyTheme = (theme) => {
        const html = document.documentElement;
        const selected = theme || localStorage.getItem(themeKey) || 'system';
        html.setAttribute('data-theme', selected);

        const activeTheme = selected === 'system' ? getPreferred() : selected;
        themeMeta.setAttribute('content', activeTheme === 'dark' ? '#0b1220' : '#ffffff');
        html.style.colorScheme = activeTheme;

        if (themeSwitcherBtn) {
            themeSwitcherBtn.innerHTML = `<i class="bi ${icons[selected]}"></i> ${labels[selected]}`;
        }

        themeOptions.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === selected);
        });
    };

    const saveTheme = (theme) => {
        localStorage.setItem(themeKey, theme);
        applyTheme(theme);
    };

    if (themeOptions.length) {
        themeOptions.forEach(btn => {
            btn.addEventListener('click', function() {
                saveTheme(this.dataset.theme);
            });
        });
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if ((localStorage.getItem(themeKey) || 'system') === 'system') {
            applyTheme('system');
        }
    });

    applyTheme(localStorage.getItem(themeKey) || 'system');
})();
