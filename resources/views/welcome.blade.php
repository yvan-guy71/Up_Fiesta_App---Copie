<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Up Fiesta - Votre plateforme d'événements au Togo</title>
    
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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Custom Tom Select Styling */
        .ts-control {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            font-weight: 500 !important;
            color: white !important;
        }
        .ts-control input {
            color: white !important;
        }
        .ts-control input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        .ts-dropdown {
            border-radius: 1rem !important;
            border: none !important;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
            padding: 0.5rem !important;
            background: white !important;
            color: #1e293b !important;
            z-index: 100 !important;
            /* Force opening upwards */
            top: auto !important;
            bottom: 100% !important;
            margin-top: 0 !important;
            margin-bottom: 0.5rem !important;
            width: auto;
        }
        .ts-dropdown .active {
            background-color: #6366f1 !important;
            color: white !important;
        }
        .ts-dropdown .option {
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
        }
        .ts-wrapper.single .ts-control::after {
            border-color: white transparent transparent transparent !important;
            right: 15px !important;
        }
        .ts-wrapper.single.input-active .ts-control::after {
            border-color: transparent transparent white transparent !important;
        }
    </style>
    @endif
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
        });
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        html.dark body {
            background-color: #020617;
            color: #e5e7eb;
        }
        /* Mode sombre : header plus lisible et moins agressif */
        html.dark header {
            background-color: rgba(15, 23, 42, 0.9);
            border-bottom-color: #1f2937;
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
            background-color: #020617;
        }
        html.dark header .text-slate-400 {
            color: #9ca3af;
        }
        /* Mode sombre : menu préférences (thème + langue) */
        html.dark header .preferences-menu {
            background-color: #020617;
            border-color: #1f2937;
        }
        html.dark header .preferences-menu .text-slate-700,
        html.dark header .preferences-menu .text-slate-500 {
            color: #e5e7eb;
        }
        html.dark header .preferences-menu .text-indigo-600 {
            color: #a5b4fc;
        }
        html.dark header .preferences-menu .bg-slate-100 {
            background-color: #111827;
        }
        html.dark header .preferences-menu .hover\:bg-slate-50:hover {
            background-color: #111827;
        }
        /* Style custom pour les selects du formulaire de recherche */
        .search-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 0;
            background-color: transparent;
        }
        [x-cloak] {
            display: none !important;
        }
        /* Mode sombre : section Prestataires à la une */
        html.dark #featured {
            background-color: #020617;
        }
        html.dark #featured .bg-white {
            background-color: #020617;
            border-color: #fff;
        }
        html.dark #featured .text-slate-500,
        html.dark #featured .text-slate-600,
        html.dark #featured .text-slate-800 {
            color: #e5e7eb;
        }
        select option {
            color: #1e293b; /* text-slate-800 */
            background-color: #f1f5f9; /* bg-slate-100 */
             font-weight: 500;
        }
        select option:checked {
             background-color: #6366f1; /* indigo */
             color: white;
         }

        /* Amélioration visuelle des menus déroulants natifs */
        .search-select {
            cursor: pointer;
            padding-right: 2.5rem !important;
        }

        /* Pour les navigateurs modernes, on peut styliser un peu plus l'intérieur */
        select {
            scrollbar-width: thin;
            scrollbar-color: #6366f1 #f1f5f9;
        }

        /* Effet au survol du conteneur du select */
        .select-wrapper:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
        }

    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <x-flash-messages />
    <header class="bg-white/80 backdrop-blur border-b border-slate-100 sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4 lg:gap-10">
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
                <div class="hidden lg:flex items-center gap-6 ml-4">
                    <a href="javascript:void(0)" onclick="focusSearch()" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">{{ __('messages.nav.find_provider') }}</a>
                    <a href="#categories" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">{{ __('messages.nav.categories') }}</a>
                    <a href="#featured" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">{{ __('messages.nav.featured') }}</a>
                    <a href="{{ route('help.how') }}" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">{{ __('messages.home.featured_more') }}</a>
                    <div class="h-4 w-[1px] bg-slate-200 mx-2"></div>
                    @if(!auth()->check() || auth()->user()->role !== 'client')
                    <a href="{{ route('register.provider') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 transition-colors">{{ __('messages.nav.pro_space') }}</a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @include('partials.notifications')
                @php
                    $locales = config('app.available_locales');
                    $currentLocale = app()->getLocale();
                @endphp
                <div class="relative">
                    <button type="button"
                            id="settings-button"
                            onclick="toggleSettingsDropdown()"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors"
                            aria-label="Préférences">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l.149.457a1 1 0 00.95.69h.48a1 1 0 01.98.804l.09.45a1 1 0 00.657.758l.434.144a1 1 0 01.593 1.31l-.17.447a1 1 0 00.21 1.052l.34.339a1 1 0 010 1.414l-.34.339a1 1 0 00-.21 1.052l.17.447a1 1 0 01-.593 1.31l-.434.144a1 1 0 00-.657.758l-.09.45a1 1 0 01-.98.804h-.48a1 1 0 00-.95.69l-.149.457c-.3.921-1.603.921-1.902 0l-.149-.457a1 1 0 00-.95-.69h-.48a1 1 0 01-.98-.804l-.09-.45a1 1 0 00-.657-.758l-.434-.144a1 1 0 01-.593-1.31l.17-.447a1 1 0 00-.21-1.052l-.34-.339a1 1 0 010-1.414l.34-.339a1 1 0 00.21-1.052l-.17-.447a1 1 0 01.593-1.31l.434-.144a1 1 0 00.657-.758l.09-.45a1 1 0 01.98-.804h.48a1 1 0 00.95-.69l.149-.457z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9a3 3 0 100 6 3 3 0 000-6z" />
                        </svg>
                    </button>
                    <div id="settings-dropdown"
                         class="preferences-menu fixed top-20 inset-x-4 sm:absolute sm:top-auto sm:inset-x-auto sm:right-0 mt-2 w-auto sm:w-52 max-w-xs rounded-xl bg-white shadow-lg border border-slate-100 text-sm z-50 hidden">
                        <a href="{{ route('help.how') }}" class="w-full block px-3 py-2 text-slate-700 hover:bg-slate-50">Comment ça marche</a>
                        <button type="button"
                                onclick="toggleTheme()"
                                class="w-full flex items-center justify-between px-3 py-2 text-slate-700 hover:bg-slate-50">
                            <span>Thèmes</span>
                            <span class="flex items-center gap-1 text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 3.25a1 1 0 0 1 1 1V6a1 1 0 1 1-2 0V4.25a1 1 0 0 1 1-1zm0 10a3 3 0 1 0-3-3 3 3 0 0 0 3 3zm8.75-3a1 1 0 0 1-1 1H18a1 1 0 0 1 0-2h1.75a1 1 0 0 1 1 1zM12 17a1 1 0 0 1 1 1v1.75a1 1 0 1 1-2 0V18a1 1 0 0 1 1-1zM7 12a1 1 0 0 1-1 1H4.25a1 1 0 0 1 0-2H6a1 1 0 0 1 1 1zm9.19 4.19a1 1 0 0 1 1.41 0l1.24 1.24a1 1 0 1 1-1.41 1.41l-1.24-1.24a1 1 0 0 1 0-1.41zM5.16 5.16a1 1 0 0 1 1.41 0L7.81 6.4A1 1 0 0 1 6.4 7.81L5.16 6.57a1 1 0 0 1 0-1.41z"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21.64 13.65A9 9 0 1 1 10.35 2.36 7 7 0 0 0 21.64 13.65z" />
                                </svg>
                            </span>
                        </button>
                        <div class="border-t border-slate-100 my-1"></div>
                        <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase">Langue</div>
                        @foreach($locales as $code => $label)
                            <button type="button"
                                    onclick="window.location.href='{{ route('locale.switch', $code) }}'"
                                    class="w-full text-left px-3 py-2 text-sm {{ $currentLocale === $code ? 'bg-slate-100 text-indigo-600 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @if (Route::has('login'))
                    @auth
                        <!-- Authenticated User View -->
                        @if(auth()->user()->role === 'client')
                            <div class="hidden sm:flex items-center gap-2">
                                <a href="{{ route('service-requests.create') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Exprimer un besoin</span>
                                </a>
                                <a href="{{ route('service-requests.create', ['type' => 'event']) }}" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-indigo-600 bg-white hover:bg-slate-50 rounded-xl border border-indigo-100">
                                    <span>Créer un événement</span>
                                </a>
                            </div>
                        @endif

                        <div class="flex items-center bg-slate-100 rounded-xl p-1 gap-1">
                            <a href="{{ route('messages.index') }}" class="flex items-center justify-center w-9 h-9 text-slate-500 hover:bg-white hover:text-indigo-600 hover:shadow-sm rounded-lg transition-all relative" title="{{ __('messages.nav.messages') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                @if(App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->exists())
                                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
                                @endif
                            </a>
                            <a href="{{ route('bookings.index') }}" class="flex items-center justify-center w-9 h-9 text-slate-500 hover:bg-white hover:text-indigo-600 hover:shadow-sm rounded-lg transition-all" title="{{ __('messages.nav.bookings') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </a>
                        </div>
                        
                        @if(auth()->user()->role === 'provider')
                            <a href="{{ url('/prestataire') }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-black rounded-xl hover:bg-indigo-700">
                                <span>DASHBOARD</span>
                            </a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ url('/up-fiesta-kygj') }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-black rounded-xl hover:bg-indigo-700">
                                <span>PANEL ADMIN</span>
                            </a>
                        @else
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold shadow-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition-colors" title="{{ __('messages.nav.logout') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                    @guest
                        <!-- Guest View -->
                        <div class="hidden sm:flex items-center gap-1">
                            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 hover:text-indigo-600 px-4 transition-all">{{ __('messages.nav.login') }}</a>
                            <a href="{{ route('register.client') }}" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-black rounded-xl hover:bg-indigo-700">
                                <span>{{ __('messages.nav.register') }}</span>
                            </a>
                        </div>
                    @endguest
                @endif
            </div>
        </nav>
    </header>

    <div id="mobile-menu" class="fixed inset-0 z-40 bg-black/40 hidden lg:hidden">
        <div id="mobile-menu-content" class="absolute left-0 top-0 h-full w-72 max-w-full bg-white shadow-xl transform -translate-x-full transition-transform duration-300">
            <div class="flex items-center justify-between px-4 py-4 border-b border-slate-100">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-8 w-auto">
                </a>
                <button type="button" onclick="toggleMobileMenu()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-4 py-4 space-y-2">
                <a href="javascript:void(0)" onclick="toggleMobileMenu(); focusSearch();" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-100">{{ __('messages.nav.find_provider') }}</a>
                <a href="#categories" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-100">{{ __('messages.nav.categories') }}</a>
                <a href="#featured" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-100">{{ __('messages.nav.featured') }}</a>
                <a href="{{ route('help.how') }}" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-100">{{ __('messages.home.featured_more') }}</a>
                @if(!auth()->check() || auth()->user()->role !== 'client')
                <a href="{{ route('register.provider') }}" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-sm font-bold text-indigo-600 hover:bg-indigo-50">{{ __('messages.nav.pro_space') }}</a>
                @endif
            </div>
            <div class="px-4 py-4 border-t border-slate-100 space-y-3">
                @if (Route::has('login'))
                    @auth
                        @if(auth()->user()->role === 'client')
                            <a href="{{ route('service-requests.create') }}" onclick="toggleMobileMenu()" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100">{{ __('messages.cta.express_need') }}</a>
                            <a href="{{ route('service-requests.create', ['type' => 'event']) }}" onclick="toggleMobileMenu()" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-indigo-600 bg-white hover:bg-slate-50 rounded-xl border border-indigo-100 shadow-sm">{{ __('messages.cta.create_event') }}</a>
                        @endif
                        <div class="flex items-center justify-between gap-3 pt-2">
                            <a href="{{ route('messages.index') }}" onclick="toggleMobileMenu()" class="flex-1 flex items-center justify-center px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                Messages
                            </a>
                            <a href="{{ route('bookings.index') }}" onclick="toggleMobileMenu()" class="flex-1 flex items-center justify-center px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                Réservations
                            </a>
                        </div>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ url('/admin') }}" onclick="toggleMobileMenu()" class="block w-full text-center mt-2 px-4 py-2.5 text-xs font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100">
                                PANEL ADMIN
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 text-xs font-bold text-slate-500 hover:text-rose-600 rounded-lg hover:bg-slate-50">Se déconnecter</button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" onclick="toggleMobileMenu()" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-100">Se connecter</a>
                        <a href="{{ route('register.client') }}" onclick="toggleMobileMenu()" class="block w-full text-center px-4 py-2.5 text-sm font-bold text-indigo-600 bg-white hover:bg-slate-50 rounded-xl border border-indigo-100 shadow-sm">Créer un compte</a>
                    @endguest
                @endif
            </div>
        </div>
    </div>

    <main>
        <!-- Hero Section -->
        <section class="relative bg-slate-900 py-24 px-4">
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80')] bg-cover bg-center opacity-40"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 to-slate-900/20"></div>
            </div>
            <div class="relative max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight">
                    {{ __('messages.home.hero_title') }}
                </h1>
                <p class="text-xl text-indigo-100 mb-10">
                    {{ __('messages.home.hero_subtitle') }}
                </p>
                @php
                    $selectedCategoryId = request('category');
                    $selectedCategoryName = 'Toutes catégories';
                    if ($selectedCategoryId) {
                        $selectedCategory = $categories->firstWhere('id', (int) $selectedCategoryId);
                        if ($selectedCategory) {
                            $selectedCategoryName = $selectedCategory->name;
                        }
                    }
                    $selectedCityId = request('city');
                    $selectedCityName = 'Toutes les villes';
                    if ($selectedCityId) {
                        $selectedCity = $cities->firstWhere('id', (int) $selectedCityId);
                        if ($selectedCity) {
                            $selectedCityName = $selectedCity->name;
                        }
                    }
                @endphp
                <form action="{{ route('search') }}" method="GET" id="search-form" class="bg-white/10 backdrop-blur-2xl p-4 rounded-3xl shadow-2xl flex flex-col lg:flex-row gap-4 max-w-5xl mx-auto transition-all duration-500 border border-white/20 hover:border-white/40">
                    <div class="flex-1 flex items-center bg-white/10 px-6 py-3 rounded-2xl border-2 border-transparent transition-all group focus-within:bg-white/20 focus-within:border-white/60 focus-within:ring-4 focus-within:ring-white/10" id="search-input-container">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-3 group-focus-within:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="q" id="search-q" value="{{ request('q') }}" placeholder="{{ __('messages.home.search_placeholder') }}" class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-white text-lg placeholder:text-white/60 font-small">
                    </div>
                    
                    <div class="lg:w-48 relative flex items-center bg-white/10 px-4 py-2 rounded-xl border border-transparent focus-within:bg-white/20 focus-within:border-white/60 transition-all select-wrapper">
                        <select id="select-kind" name="kind" class="search-select w-full bg-transparent border-none focus:ring-0 text-white font-medium pr-6">
                            <option value="" class="text-slate-900">Tous types</option>
                            <option value="{{ \App\Models\ServiceCategory::KIND_PRESTATIONS }}" {{ request('kind') === \App\Models\ServiceCategory::KIND_PRESTATIONS ? 'selected' : '' }} class="text-slate-900">Prestations</option>
                            <option value="{{ \App\Models\ServiceCategory::KIND_DOMESTIQUES }}" {{ request('kind') === \App\Models\ServiceCategory::KIND_DOMESTIQUES ? 'selected' : '' }} class="text-slate-900">Domestiques</option>
                        </select>
                    </div>

                    <div class="lg:w-48 relative flex items-center bg-white/10 px-4 py-2 rounded-xl border border-transparent focus-within:bg-white/20 focus-within:border-white/60 transition-all select-wrapper">
                        <select id="select-category" name="category" class="search-select w-full bg-transparent border-none focus:ring-0 text-white font-medium pr-6">
                            <option value="" class="text-slate-900">Toutes catégories</option>
                            @foreach($searchCategories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }} class="text-slate-900" data-kind="{{ $category->kind }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:w-48 relative flex items-center bg-white/10 px-4 py-2 rounded-xl border border-transparent focus-within:bg-white/20 focus-within:border-white/60 transition-all select-wrapper">
                        <select id="select-city" name="city" class="search-select w-full bg-transparent border-none focus:ring-0 text-white font-medium pr-6">
                            <option value="" class="text-slate-900">Toutes les villes</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }} class="text-slate-900">
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-black text-lg flex items-center justify-center gap-2">
                        {{ __('messages.home.search_button') }}
                    </button>
                </form>

                <!-- Suggestions de recherche -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <span class="text-indigo-100/80 text-sm font-bold uppercase tracking-wider mr-2">{{ __('messages.home.search_popular') }}</span>
                    @php
                        $suggestions = ['Traiteur', 'Menuisier', 'DJ', 'Plombier', 'Photographe'];
                    @endphp
                    @foreach($suggestions as $suggestion)
                        <button type="button" onclick="setSearchValue('{{ $suggestion }}')" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-4 py-1.5 rounded-full text-sm font-bold hover:bg-white hover:text-indigo-600 transition-all hover:-translate-y-1 shadow-sm">
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Categories section starts here -->
        <section id="categories" class="max-w-7xl mx-auto py-20 px-4 scroll-mt-20">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-3xl font-bold">{{ __('messages.categories.browse_title') }}</h2>
                <a href="{{ route('categories.index') }}" class="text-indigo-600 font-semibold hover:underline">{{ __('messages.categories.view_all') }}</a>
            </div>
            <!-- quick kind filters -->
            <div class="flex gap-4 mb-8">
                <a href="{{ route('home', array_merge(request()->except('kind'), ['kind' => \App\Models\ServiceCategory::KIND_PRESTATIONS])) }}#categories" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">
                    
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h3m10 0h3a1 1 0 011 1v3M3 16v3a1 1 0 001 1h3m10 0h3a1 1 0 001-1v-3" />
                    </svg>
                    {{ __('messages.categories.kind_prestations') }}
                </a>
                <a href="{{ route('home', array_merge(request()->except('kind'), ['kind' => \App\Models\ServiceCategory::KIND_DOMESTIQUES])) }}#categories" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-full hover:bg-green-700 transition">
                    
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M5 14h14l1 6H4l1-6z" />
                    </svg>
                    {{ __('messages.categories.kind_domestiques') }}
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
             @foreach($homeCategories as $category)
             @php
               $selected = request()->filled('category') && request()->category == $category->id;
               $categoryImages = [
                // Événementiel
                'traiteur' => 'images/categories/perto.jpg',
                'decoration' => 'images/categories/deco.png',
                'photographie-video' => 'images/categories/photo.jpg',   
                'animation-dj' => 'images/categories/DJ.jpg',
                'location-salle' => 'images/categories/location.webp',
                'securite' => 'images/categories/secure.jpeg',
                'maquillage-coiffure' => 'images/categories/coiffure.jpg',
                'location-voiture' => 'images/categories/voiture.jpeg',
                'hotesse-accueil' => 'images/categories/hotesse.jpg',
                
                // Services Professionnels
                'maconnerie' => 'images/categories/macon.webp',
                'menuiserie' => 'images/categories/menuisier.jpg',
                'cuisinier-domicile' => 'images/categories/cuisinier.webp',
                'plomberie' => 'images/categories/plombier.jpg',
                'electricite' => 'images/categories/electricien.webp',
                'peinture' => 'images/categories/peintre.jpg',
                'climatisation' => 'images/categories/clim.jpg',
                'entretien-nettoyage' => 'images/categories/nettoyage.webp',
                'mecanique' => 'images/categories/mecanique.jpeg',
                'transport-logistique' => 'images/categories/transport.jpeg',
            ];
              $imagePath = $categoryImages[$category->slug] ?? 'images/categories/default.jpg';
             @endphp
            <div class="relative group rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition h-40 md:h-48 {{ $selected ? 'ring-4 ring-indigo-500' : '' }}">
                <!-- full clickable area for category detail page -->
                <a href="{{ route('categories.show', $category->id) }}" class="absolute inset-0 z-0"></a>
                <!-- quick type filter icon -->
                <a href="{{ route('categories.index', ['kind' => $category->kind]) }}" class="absolute top-2 left-2 z-10 bg-white/70 p-1 rounded-full hover:bg-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h3m10 0h3a1 1 0 011 1v3M3 16v3a1 1 0 001 1h3m10 0h3a1 1 0 001-1v-3" />
                    </svg>
                </a>
            <img src="{{ asset($imagePath) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4 bg-black/40 text-white transition-opacity duration-700 group-hover:opacity-0">
                <h3 class="font-bold text-lg md:text-xl">{{ $category->name }}</h3>
                <p class="text-sm opacity-90 mt-1">{{ $category->providers_count }} professionnels disponibles</p>
            </div>
            </div>
             @endforeach
             </div>
        </section>

        <section id="featured" class="bg-slate-100 py-20 px-4 scroll-mt-20">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-12">
                    <h2 class="text-3xl font-bold">{{ __('messages.home.featured_title') }}</h2>
                    <a href="{{ route('categories.index') }}" class="text-indigo-600 font-semibold hover:underline">{{ __('messages.home.featured_more') }}</a>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($featuredProviders as $provider)
                        @include('partials.provider-card', ['provider' => $provider])
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-slate-500">{{ __('messages.home.featured_empty') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        @guest
        <!-- CTA Section -->
        <section class="py-20 px-4 text-center">
            <div class="max-w-3xl mx-auto bg-indigo-600 rounded-3xl p-12">
                <h2 class="text-3xl font-bold text-white mb-6">{{ __('messages.cta.pro_title') }}</h2>
                <p class="text-indigo-100 mb-10 text-lg">{{ __('messages.cta.pro_subtitle') }}</p>
                <a href="{{ route('register.provider') }}" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-slate-100 transition shadow-lg">{{ __('messages.cta.pro_signup') }}</a>
            </div>
        </section>
        @endguest
    </main>

    <footer class="bg-slate-900 text-slate-300 py-16 px-4">
        <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-12">
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-8 w-auto brightness-0 invert">
                </div>
                <p class="text-sm leading-relaxed mb-6">{{ __('messages.footer.description') }}</p>
                <div class="flex gap-4">
                    <a href="https://facebook.com/upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-indigo-600 transition-colors group" title="Facebook">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://tiktok.com/@upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-black transition-colors group" title="TikTok">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </a>
                    <a href="https://instagram.com/upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-rose-600 transition-colors group" title="Instagram">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.332 3.608 1.308.975.975 1.245 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.332 2.633-1.308 3.608-.975.975-2.242 1.245-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.332-3.608-1.308-.975-.975-1.245-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.332-2.633 1.308-3.608.975-.975 2.242-1.245 3.608-1.308 1.266-.058 1.646-.07 4.85-.07zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.272 2.69.072 7.053.014 8.333 0 8.741 0 12s.014 3.667.072 4.947c.2 4.353 2.62 6.777 6.981 6.977 1.28.057 1.688.071 4.947.071s3.667-.014 4.947-.072c4.351-.2 6.777-2.62 6.977-6.981.057-1.28.071-1.688.071-4.947s-.014-3.667-.072-4.947c-.2-4.351-2.62-6.777-6.981-6.977C15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z"/>
                        </svg>
                    </a>
                    <a href="https://linkedin.com/company/upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors group" title="LinkedIn">
                        <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div>
                <h5 class="text-white font-bold mb-6">{{ __('messages.footer.quick_nav') }}</h5>
                <ul class="space-y-4 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-indigo-400 transition-colors">{{ __('messages.footer.home') }}</a></li>
                    <li><a href="#results" class="hover:text-indigo-400 transition-colors">{{ __('messages.nav.find_provider') }}</a></li>
                    <li><a href="#categories" class="hover:text-indigo-400 transition-colors">{{ __('messages.footer.categories') }}</a></li>
                    <li><a href="#featured" class="hover:text-indigo-400 transition-colors">{{ __('messages.home.featured_title') }}</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-6">{{ __('messages.footer.pro_space') }}</h5>
                <ul class="space-y-4 text-sm">
                    <li><a href="{{ route('register.provider') }}" class="hover:text-indigo-400 transition-colors font-bold text-indigo-400">{{ __('messages.footer.become_provider') }}</a></li>
                    <li><a href="{{ url('/prestataire') }}" class="hover:text-indigo-400 transition-colors">{{ __('messages.footer.provider_login') }}</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold mb-6">{{ __('messages.footer.contact') }}</h5>
                <ul class="space-y-4 text-sm">
                    <li>
                        <a href="{{ route('contact') }}" class="flex items-center gap-3 hover:text-indigo-400 transition-colors group">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center group-hover:bg-indigo-500/40 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <span class="font-bold">Contacter Up Fiesta</span>
                        </a>
                    </li>
                    <li class="flex items-start gap-3 opacity-70">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Lomé, Togo</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Upfiesta.proj@gmail.com</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>+228 99 46 25 51</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto border-t border-slate-800 mt-16 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
            <p>&copy; 2026 Up Fiesta.</p>
            <div class="flex gap-6">
                <a href="{{ route('legal.cgu') }}" class="hover:text-white transition-colors">CGU</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-white transition-colors">Confidentialité</a>
            </div>
        </div>
    </footer>
    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Réserver un service <span id="modalProviderName" class="text-indigo-600"></span></h2>
                <button onclick="closeBookingModal()" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="bookingForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Date souhaitée</label>
                        <input type="date" name="event_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Détails du besoin</label>
                        <textarea name="event_details" rows="3" placeholder="Lieu, description de la tâche, nombre de personnes..." class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex gap-4">
                    <button type="button" onclick="closeBookingModal()" class="flex-1 px-6 py-3 border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50 transition">Annuler</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">Confirmer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const content = document.getElementById('mobile-menu-content');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('-translate-x-full');
                }, 10);
            } else {
                content.classList.add('-translate-x-full');
                setTimeout(() => {
                    menu.classList.add('hidden');
                }, 300);
            }
        }

        function setSearchValue(value) {
            const searchInput = document.getElementById('search-q');
            searchInput.value = value;
            
            // Petit effet visuel lors du clic sur une suggestion
            const container = document.getElementById('search-input-container');
            container.classList.add('ring-4', 'ring-white/30');
            setTimeout(() => {
                container.classList.remove('ring-4', 'ring-white/30');
                document.getElementById('search-form').submit();
            }, 2000);
        }

        function focusSearch() {
            const searchInput = document.getElementById('search-q');
            const container = document.getElementById('search-input-container');
            const form = document.getElementById('search-form');

            // Défilement vers le haut (Hero section)
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            // Petit délai pour attendre le début du défilement
            setTimeout(() => {
                searchInput.focus();
                
                // Animation d'accentuation
                container.classList.add('border-indigo-500', 'ring-4', 'ring-indigo-100');
                form.classList.add('scale-[1.02]');
                
                // Retrait de l'animation après 2 secondes
                setTimeout(() => {
                    container.classList.remove('border-indigo-500', 'ring-4', 'ring-indigo-100');
                    form.classList.remove('scale-[1.02]');
                }, 2000);
            }, 500);
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }

        // Initialize Tom Select
        document.addEventListener('DOMContentLoaded', () => {
            const config = {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                allowEmptyOption: true,
            };

            const kindSelect = new TomSelect("#select-kind", config);
            const categorySelect = new TomSelect("#select-category", config);
            new TomSelect("#select-city", config);

            // Store all category options
            const allCategoryOptions = Array.from(document.querySelectorAll('#select-category option')).map(opt => ({
                value: opt.value,
                text: opt.text,
                kind: opt.dataset.kind
            }));

            function filterCategories() {
                const kind = kindSelect.getValue();
                if (!kind) {
                    categorySelect.clearOptions();
                    categorySelect.addOptions(allCategoryOptions);
                    return;
                }
                
                const filtered = allCategoryOptions.filter(opt => !opt.kind || opt.kind === kind);
                categorySelect.clearOptions();
                categorySelect.addOptions(filtered);
            }

            kindSelect.on('change', filterCategories);
            
            // Initial filter if kind is selected
            if (kindSelect.getValue()) {
                filterCategories();
                @if(request()->filled('category'))
                    categorySelect.setValue("{{ request('category') }}");
                @endif
            }
        });

        function openBookingModal(id, name) {
            @if(!auth()->check())
            window.location.href = "{{ route('login') }}";
            return;
            @endif
            
            document.getElementById('modalProviderName').innerText = name;
            document.getElementById('bookingForm').action = "/reserver/" + id;
            document.getElementById('bookingModal').classList.remove('hidden');
            document.getElementById('bookingModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.getElementById('bookingModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close on click outside
        window.onclick = function(event) {
            let modal = document.getElementById('bookingModal');
            if (event.target == modal) {
                closeBookingModal();
            }
        }
    </script>
<script src='https://cdn.jotfor.ms/agent/embedjs/019c89b017677f818ad04959bf7bab1f8c44/embed.js'></script>
</body>
</html>
