<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.register_button') }} - Up Fiesta</title>
    
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
                <h2 class="text-3xl font-black text-slate-900 dark:text-white">{{ __('auth.register_title') }}</h2>
                <p class="mt-2 text-slate-500 dark:text-slate-400">{{ __('auth.register_subtitle') }}</p>
            </div>

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('register.client.post') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">{{ __('auth.register_name') }}</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="Yvan Guy">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">Téléphone</label>
                        <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="+228 90 00 00 00">
                        <input type="hidden" name="full_phone" id="full_phone">
                        @error('phone') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">{{ __('auth.login_email') }}</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="votre@email.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">{{ __('auth.register_password') }}</label>
                        <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">{{ __('auth.register_password_confirm') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-blue-600 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-slate-800 shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 transition-all">
                        {{ __('auth.register_button') }}
                    </button>
                </div>
            </form>

            <div class="text-center pt-6 border-t border-slate-100 dark:border-slate-700">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    {{ __('auth.register_has_account') }}
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">{{ __('auth.register_login') }}</a>
                </p>
                <div class="mt-4">
                    <a href="{{ route('register.provider') }}" class="text-xs font-bold text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300">{{ __('auth.register_provider_cta') }}</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.intlTelInput) {
                const phoneInput = document.querySelector("#phone");
                const fullPhoneInput = document.querySelector("#full_phone");
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: "tg",
                    preferredCountries: ["tg", "bj", "gh", "ci", "fr"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
                });
                const update = () => fullPhoneInput.value = iti.getNumber();
                phoneInput.addEventListener('change', update);
                phoneInput.addEventListener('keyup', update);
            }
        });
    </script>
</body>
</html>
