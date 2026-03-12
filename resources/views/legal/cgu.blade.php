<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('legal.cgu_title') }} - Up Fiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-8 w-auto">
                </a>
                <a href="/" class="text-sm font-bold text-indigo-600 hover:text-indigo-500">{{ __('legal.back_home') }}</a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xl shadow-slate-200 border border-slate-100">
            <h1 class="text-4xl font-black text-slate-900 mb-8">{{ __('legal.cgu_title') }}</h1>
            <div class="mb-8">
                <a href="{{ route('help.how') }}" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl">
                    Comment ça marche
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.293 15.95l5.657-5.657a1 1 0 000-1.414L10.293 3.222a1 1 0 011.414-1.414l6.364 6.364a3 3 0 010 4.243l-6.364 6.364a1 1 0 01-1.414-1.414z" />
                    </svg>
                </a>
            </div>
            
            <div class="prose prose-slate max-w-none space-y-6">
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">1. {{ __('legal_content.cgu.intro_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.intro_p1') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">2. {{ __('legal_content.cgu.services_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.services_p1') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">3. {{ __('legal_content.cgu.inscription_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.inscription_p1') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">4. {{ __('legal_content.cgu.obligations_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.obligations_p1') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">5. {{ __('legal_content.cgu.paiements_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.paiements_p1') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">6. {{ __('legal_content.cgu.responsabilite_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.responsabilite_p1') }}</p>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.responsabilite_p2') }}</p>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.responsabilite_p3') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">7. {{ __('legal_content.cgu.modification_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.modification_p1') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal.contact_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.contact_p1') }}</p>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.cgu.contact_p2') }} <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Up Fiesta</a>.</p>
                </section>
                
                <p class="text-sm text-slate-400 mt-12 italic">{{ __('legal.last_update', ['date' => '5 Mars 2026']) }}</p>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} Up Fiesta. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
