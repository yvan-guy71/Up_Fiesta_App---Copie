<script>
    // Initialize dark mode as soon as possible to avoid flash of light mode
    (function() {
        const stored = localStorage.getItem('theme');
        const root = document.documentElement;
        if (stored === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const root = document.documentElement;

        window.applyTheme = function(mode) {
            if (mode === 'dark') {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }
        };

        window.toggleTheme = function() {
            const current = root.classList.contains('dark') ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            window.applyTheme(next);
        };

        window.toggleSettingsDropdown = function() {
            const button = document.getElementById('settings-button');
            const menu = document.getElementById('settings-dropdown');
            if (!button || !menu) return;
            menu.classList.toggle('hidden');
        };

        document.addEventListener('click', function(e) {
            const button = document.getElementById('settings-button');
            const menu = document.getElementById('settings-dropdown');
            if (!button || !menu) return;
            if (!menu.classList.contains('hidden') &&
                !menu.contains(e.target) &&
                !button.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    });
</script>
