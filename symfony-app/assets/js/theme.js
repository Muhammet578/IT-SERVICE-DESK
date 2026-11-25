/*
 * IT SERVICE DESK
 * Classic / Vice City Theme Switcher
 * Compatible with Symfony UX Turbo
 */

(() => {
    // Не подключаем обработчики повторно.
    if (window.__itDeskThemeInitialized) {
        return;
    }

    window.__itDeskThemeInitialized = true;

    const STORAGE_KEY = 'itdesk-theme';

    const getTheme = () => {
        return document.documentElement.dataset.theme === 'vice-city'
            ? 'vice-city'
            : 'classic';
    };

    // Обновляем кнопку в текущем Navbar.
    const updateButton = () => {
        const button = document.getElementById('theme-toggle');

        if (!button) {
            return;
        }

        const isViceCity = getTheme() === 'vice-city';

        button.textContent = isViceCity
            ? '🌴 Vice City'
            : '☀️ Classic';

        button.setAttribute(
            'aria-label',
            isViceCity
                ? 'Переключить на светлую тему Classic'
                : 'Переключить на тёмную тему Vice City'
        );

        button.setAttribute(
            'aria-pressed',
            String(isViceCity)
        );
    };

    // Применяем и сохраняем тему.
    const applyTheme = (theme) => {
        document.documentElement.dataset.theme = theme;

        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (error) {
            // Тема работает даже без localStorage.
        }

        updateButton();
    };

    // Обработчик на document сохраняется
    // даже при замене Navbar через Turbo.
    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const button = event.target.closest('#theme-toggle');

        if (!button) {
            return;
        }

        const nextTheme = getTheme() === 'classic'
            ? 'vice-city'
            : 'classic';

        const reduceMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;

        // Плавное переключение темы.
        if (
            typeof document.startViewTransition === 'function'
            && !reduceMotion
        ) {
            document.startViewTransition(() => {
                applyTheme(nextTheme);
            });
        } else {
            applyTheme(nextTheme);
        }
    });

    // Обычная загрузка страницы.
    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            updateButton
        );
    } else {
        updateButton();
    }

    // Повторная синхронизация после переходов Turbo.
    document.addEventListener('turbo:load', updateButton);

    // Синхронизация перед сохранением страницы Turbo.
    document.addEventListener('turbo:render', updateButton);
})();