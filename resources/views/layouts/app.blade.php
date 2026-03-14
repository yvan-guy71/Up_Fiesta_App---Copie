<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Up Fiesta</title>
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-EBCV83H4WN"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-EBCV83H4WN');
    </script>

    <!-- PWA -->
    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script>
            tailwind.config = {
                darkMode: 'class',
            };
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Dark Mode - Body */
        html.dark body {
            background-color: #020617;
            color: #e5e7eb;
        }

        /* Dark Mode - Header */
        html.dark header {
            background-color: rgba(15, 23, 42, 0.95) !important;
            border-bottom-color: #1f2937 !important;
            backdrop-filter: blur(10px);
        }

        html.dark header .text-slate-600,
        html.dark header .text-slate-700,
        html.dark header .text-slate-800,
        html.dark header .text-slate-900 {
            color: #e5e7eb;
        }

        html.dark header .border-slate-100,
        html.dark header .border-slate-200 {
            border-color: #1f2937;
        }

        html.dark header .bg-slate-100 {
            background-color: #111827;
        }

        html.dark header .text-slate-400 {
            color: #9ca3af;
        }

        html.dark header .bg-white {
            background-color: #111827;
            border-color: #1f2937;
        }

        html.dark header .hover\:bg-slate-50:hover {
            background-color: #111827;
        }

        /* Dark Mode - Sections */
        html.dark .bg-slate-50 {
            background-color: #020617;
        }

        html.dark .bg-slate-100 {
            background-color: #111827;
        }

        html.dark .bg-white {
            background-color: #1a1f2e;
            border-color: #2d3748;
        }

        html.dark .border-slate-200 {
            border-color: #2d3748;
        }

        html.dark .border-slate-100 {
            border-color: #1f2937;
        }

        html.dark .text-slate-500,
        html.dark .text-slate-600,
        html.dark .text-slate-700,
        html.dark .text-slate-800,
        html.dark .text-slate-900 {
            color: #e5e7eb;
        }

        html.dark .text-slate-400 {
            color: #9ca3af;
        }

        html.dark .placeholder-slate-400::placeholder {
            color: #6b7280 !important;
        }

        html.dark input,
        html.dark select,
        html.dark textarea {
            background-color: #111827;
            color: #e5e7eb;
            border-color: #2d3748;
        }

        html.dark input::placeholder,
        html.dark select::placeholder,
        html.dark textarea::placeholder {
            color: #6b7280;
        }

        html.dark input:focus,
        html.dark select:focus,
        html.dark textarea:focus {
            background-color: #1a1f2e;
            border-color: #4f46e5;
        }

        /* Dark Mode - Cards */
        html.dark .bg-gradient-to-b {
            background-color: #020617;
        }

        html.dark .shadow-lg,
        html.dark .shadow-xl,
        html.dark .shadow-sm {
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
        }

        /* Dark Mode - Buttons & Links */
        html.dark .hover\:bg-slate-200:hover {
            background-color: #1f2937;
        }

        html.dark .hover\:text-slate-900:hover {
            color: #e5e7eb;
        }

        /* Dark Mode - Footer */
        html.dark footer {
            background-color: #020617;
        }

        /* Dark Mode - Select Options */
        select option {
            background-color: #1e293b;
            color: #f1f5f9;
        }

        select option:checked {
            background-color: #6366f1;
            color: white;
        }

        html.dark select option {
            background-color: #1e293b;
            color: #f1f5f9;
        }

        /* Dark Mode - Tables & Lists */
        html.dark .divide-slate-200 {
            border-color: #2d3748;
        }

        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color;
            transition-duration: 200ms;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    @include('partials.header')
    
    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stored = localStorage.getItem('theme');
            const root = document.documentElement;

            function applyTheme(mode) {
                if (mode === 'dark') {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
            }

            applyTheme(stored === 'dark' ? 'dark' : 'light');

            window.toggleTheme = function() {
                const current = root.classList.contains('dark') ? 'dark' : 'light';
                const next = current === 'dark' ? 'light' : 'dark';
                localStorage.setItem('theme', next);
                applyTheme(next);
            };
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
