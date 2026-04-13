<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login_button') }} - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#004aad">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-900 dark:text-white">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-800 p-10 rounded-3xl shadow-xl shadow-slate-200 dark:shadow-black/30 border border-slate-100 dark:border-slate-700">
            <div class="text-center">
                <a href="/" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-12 w-auto">
                </a>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white">{{ __('auth.login_title') }}</h2>
                <p class="mt-2 text-slate-500 dark:text-slate-400">{{ __('auth.login_subtitle') }}</p>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-bold flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-bold flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('status'))
                <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-bold flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('suggest_provider_login'))
                <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-200 text-sm font-bold">
                    <p class="mb-2">{{ __('auth.login_provider_hint') }}</p>
                    <a href="/prestataire/login" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors shadow-md shadow-blue-500/30">
                        {{ __('auth.login_go_provider') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">Email ou Téléphone</label>
                        <input id="email" name="email" type="text" required class="w-full px-4 py-3 rounded-xl border @error('email') border-red-500 @else border-slate-200 dark:border-slate-600 @enderror bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="votre@email.com ou +228 90 00 00 00" value="{{ old('email') }}">
                        <button type="button" id="use-phone-button" class="mt-2 text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Saisir un numéro avec indicatif</button>
                        <div id="phone-login-wrapper" class="mt-2 hidden">
                            <input id="phone_login" type="tel" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="+228 90 00 00 00">
                            <input type="hidden" id="phone_full_login">
                            <p class="text-[10px] text-slate-500 mt-1">Le champ “Email ou Téléphone” sera automatiquement rempli avec votre numéro au bon format.</p>
                        </div>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">{{ __('auth.login_password') }}</label>
                        <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border @error('email') border-red-500 @else border-slate-200 dark:border-slate-600 @enderror bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-slate-600 dark:text-slate-300">{{ __('auth.login_remember') }}</label>
                    </div>
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ __('auth.login_forgot') }}</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-800 shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 transition-all">
                        {{ __('auth.login_button') }}
                    </button>
                </div>

                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-slate-800 text-slate-400 dark:text-slate-500">OU</span>
                    </div>
                </div>

                <div>
                    <a href="{{ route('login.google') }}" class="w-full flex justify-center items-center gap-3 py-4 px-4 border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 text-sm font-black rounded-2xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none transition-all shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Se connecter avec Google
                    </a>
                </div>
            </form>

            <div class="text-center pt-6 border-t border-slate-200 dark:border-slate-700">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    {{ __('auth.login_no_account') }}
                    <a href="{{ route('register.client') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ __('auth.login_create_account') }}</a>
                </p>
                <div class="mt-4 flex flex-col gap-2">
                    <a href="{{ route('register.provider') }}" class="text-xs font-bold text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">{{ __('auth.login_provider_cta') }}</a>
                    <a href="{{ route('home') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ __('auth.back_home') }}</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('use-phone-button');
            const wrapper = document.getElementById('phone-login-wrapper');
            const phoneInput = document.getElementById('phone_login');
            const phoneFull = document.getElementById('phone_full_login');
            const emailInput = document.getElementById('email');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    wrapper.classList.toggle('hidden');
                    if (!wrapper.classList.contains('hidden')) {
                        phoneInput.focus();
                    }
                });
            }

            function initIntlTel() {
                if (!window.intlTelInput || !phoneInput) return;
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: "tg",
                    preferredCountries: ["tg", "bj", "gh", "ci", "fr"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
                });
                const update = () => {
                    phoneFull.value = iti.getNumber();
                    if (phoneFull.value) {
                        emailInput.value = phoneFull.value;
                    }
                };
                phoneInput.addEventListener('change', update);
                phoneInput.addEventListener('keyup', update);
            }

            initIntlTel();
        });
    </script>
</body>
</html>
