<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.forgot_title') }} - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-slate-100">
            <div class="text-center">
                <a href="/" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-12 w-auto">
                </a>
                <h2 class="text-3xl font-black text-slate-900">{{ __('auth.forgot_title') }}</h2>
                <p class="mt-2 text-slate-500">{{ __('auth.forgot_subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-bold flex items-center gap-3 animate-bounce">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-bold flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Une erreur est survenue lors de l'envoi.
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1">{{ __('auth.forgot_email') }}</label>
                        <input id="email" name="email" type="email" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all @error('email') border-red-500 @enderror" placeholder="votre@email.com" value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-100 transition-all">
                        {{ __('auth.forgot_button') }}
                    </button>
                </div>
            </form>

            <div class="text-center pt-6 border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    {{ __('auth.forgot_remember') }}
                    <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500">{{ __('auth.login_button') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
