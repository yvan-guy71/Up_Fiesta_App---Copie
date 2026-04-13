<header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur border-b border-slate-100 dark:border-slate-800 sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <div class="flex items-center gap-4 lg:gap-10">
            <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <div class="hidden lg:flex items-center gap-6 ml-4">
                <a href="javascript:void(0)" onclick="focusSearch()" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('messages.nav.find_provider') }}</a>
                <a href="#categories" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('messages.nav.categories') }}</a>
                <a href="#featured" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('messages.nav.featured') }}</a>
                <a href="{{ route('help.how') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('messages.nav.how_it_works') }}</a>
                <div class="h-4 w-[1px] bg-slate-200 dark:bg-slate-700 mx-2"></div>
                @if(!auth()->check() || auth()->user()->role !== 'client')
                <a href="{{ route('register.provider') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">{{ __('messages.nav.pro_space') }}</a>
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
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                        aria-label="Préférences">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l.149.457a1 1 0 00.95.69h.48a1 1 0 01.98.804l.09.45a1 1 0 00.657.758l.434.144a1 1 0 01.593 1.31l-.17.447a1 1 0 00.21 1.052l.34.339a1 1 0 010 1.414l-.34.339a1 1 0 00-.21 1.052l.17.447a1 1 0 01-.593 1.31l-.434.144a1 1 0 00-.657.758l-.09.45a1 1 0 01-.98.804h-.48a1 1 0 00-.95.69l-.149.457c-.3.921-1.603.921-1.902 0l-.149-.457a1 1 0 00-.95-.69h-.48a1 1 0 01-.98-.804l-.09-.45a1 1 0 00-.657-.758l-.434-.144a1 1 0 01-.593-1.31l.17-.447a1 1 0 00-.21-1.052l-.34-.339a1 1 0 010-1.414l.34-.339a1 1 0 00.21-1.052l-.17-.447a1 1 0 01.593-1.31l.434-.144a1 1 0 00.657-.758l.09-.45a1 1 0 01.98-.804h.48a1 1 0 00.95-.69l.149-.457z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9a3 3 0 100 6 3 3 0 000-6z" />
                    </svg>
                </button>
                <div id="settings-dropdown"
                     class="preferences-menu fixed top-20 inset-x-4 sm:absolute sm:top-auto sm:inset-x-auto sm:right-0 mt-2 w-auto sm:w-52 max-w-xs rounded-xl bg-white dark:bg-slate-800 shadow-lg dark:shadow-slate-900 border border-slate-100 dark:border-slate-700 text-sm z-50 hidden">
                    <a href="{{ route('help.how') }}" class="w-full block px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">{{ __('messages.nav.how_it_works') }}</a>
                    <button type="button"
                            onclick="toggleTheme()"
                            class="w-full flex items-center justify-between px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                        <span>{{ __('messages.nav.dark_mode') }}</span>
                        <span class="flex items-center gap-1 text-slate-500 dark:text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3.25a1 1 0 0 1 1 1V6a1 1 0 1 1-2 0V4.25a1 1 0 0 1 1-1zm0 10a3 3 0 1 0-3-3 3 3 0 0 0 3 3zm8.75-3a1 1 0 0 1-1 1H18a1 1 0 0 1 0-2h1.75a1 1 0 0 1 1 1zM12 17a1 1 0 0 1 1 1v1.75a1 1 0 1 1-2 0V18a1 1 0 0 1 1-1zM7 12a1 1 0 0 1-1 1H4.25a1 1 0 0 1 0-2H6a1 1 0 0 1 1 1zm9.19 4.19a1 1 0 0 1 1.41 0l1.24 1.24a1 1 0 1 1-1.41 1.41l-1.24-1.24a1 1 0 0 1 0-1.41zM5.16 5.16a1 1 0 0 1 1.41 0L7.81 6.4A1 1 0 0 1 6.4 7.81L5.16 6.57a1 1 0 0 1 0-1.41z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21.64 13.65A9 9 0 1 1 10.35 2.36 7 7 0 0 0 21.64 13.65z" />
                            </svg>
                        </span>
                    </button>
                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                    <div class="px-3 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Langue</div>
                    @foreach($locales as $code => $label)
                        <button type="button"
                                onclick="window.location.href='{{ route('locale.switch', $code) }}'"
                                class="w-full text-left px-3 py-2 text-sm {{ $currentLocale === $code ? 'bg-slate-100 dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            @if (Route::has('login'))
                @auth
                    <!-- Authenticated User View -->
                    @php
                        $dashboardUrl = '#';
                        if (auth()->user()->role === 'admin') {
                            $dashboardUrl = '/up-fiesta-kygj';
                        } elseif (auth()->user()->role === 'provider') {
                            $dashboardUrl = '/prestataire';
                        } else {
                            $dashboardUrl = route('bookings.index');
                        }
                    @endphp
                    <a href="{{ $dashboardUrl }}" class="p-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                        </svg>
                    </a>
                @else
                    <!-- Guest View -->
                    <a href="{{ route('login') }}" class="p-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                        </svg>
                    </a>
                @endauth
            @endif
        </div>
    </nav>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
        <div class="px-4 py-4 space-y-3">
            <a href="javascript:void(0)" onclick="focusSearch(); toggleMobileMenu()" class="block px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">{{ __('messages.nav.find_provider') }}</a>
            <a href="#categories" onclick="toggleMobileMenu()" class="block px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">{{ __('messages.nav.categories') }}</a>
            <a href="#featured" onclick="toggleMobileMenu()" class="block px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">{{ __('messages.nav.featured') }}</a>
            <a href="{{ route('help.how') }}" class="block px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">{{ __('messages.nav.how_it_works') }}</a>
            @if(!auth()->check() || auth()->user()->role !== 'client')
                <div class="border-t border-slate-100 dark:border-slate-700 pt-3">
                    <a href="{{ route('register.provider') }}" class="block px-4 py-2 rounded-lg text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">{{ __('messages.nav.pro_space') }}</a>
                </div>
            @endif
        </div>
    </div></header>

<script>
    function toggleSettingsDropdown() {
        const dropdown = document.getElementById('settings-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    function toggleTheme() {
        const root = document.documentElement;
        const isDark = root.classList.contains('dark');
        if (isDark) {
            root.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            root.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    }

    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenu) {
            mobileMenu.classList.toggle('hidden');
        }
    }

    function focusSearch() {
        // Focus search function
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const settingsButton = document.getElementById('settings-button');
        const dropdown = document.getElementById('settings-dropdown');
        if (dropdown && !dropdown.contains(event.target) && !settingsButton.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Initialize dark mode from localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
        const stored = localStorage.getItem('theme');
        if (stored === 'dark') {
            document.documentElement.classList.add('dark');
        }
    });
</script>
