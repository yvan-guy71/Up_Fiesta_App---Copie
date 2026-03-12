<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.register_button') }} - Up Fiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-slate-100">
            <div class="text-center">
                <a href="/" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-12 w-auto">
                </a>
                <h2 class="text-3xl font-black text-slate-900">{{ __('auth.register_title') }}</h2>
                <p class="mt-2 text-slate-500">{{ __('auth.register_subtitle') }}</p>
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
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-1">{{ __('auth.register_name') }}</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="Yvan Guy">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-bold text-slate-700 mb-1">Téléphone</label>
                        <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="+228 90 00 00 00">
                        <input type="hidden" name="full_phone" id="full_phone">
                        @error('phone') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1">{{ __('auth.login_email') }}</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="votre@email.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-1">{{ __('auth.register_password') }}</label>
                        <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1">{{ __('auth.register_password_confirm') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-100 transition-all">
                        {{ __('auth.register_button') }}
                    </button>
                </div>
            </form>

            <div class="text-center pt-6 border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    {{ __('auth.register_has_account') }}
                    <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500">{{ __('auth.register_login') }}</a>
                </p>
                <div class="mt-4">
                    <a href="{{ route('register.provider') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600">{{ __('auth.register_provider_cta') }}</a>
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
