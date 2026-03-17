<!DOCTYPE html>
<html lang="fr" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        html.dark body { background-color: #020617; color: #e5e7eb; }
        html.dark .bg-white { background-color: #1a1f2e; }
        html.dark .bg-slate-50 { background-color: #1e293b; }
        html.dark .border-slate-100 { border-color: #334155; }
        html.dark .text-slate-900 { color: #f1f5f9; }
        html.dark .text-slate-500 { color: #cbd5e1; }
        html.dark .text-slate-400 { color: #94a3b8; }
        html.dark .text-slate-600 { color: #cbd5e1; }
        html.dark .text-slate-300 { color: #cbd5e1; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <header class="bg-white border-b border-slate-100 py-6 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center gap-6">
                <a href="{{ route('categories.index') }}" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition">Toutes les catégories</a>
                <a href="/" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition">Accueil</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-16 px-4">
        <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <span class="text-indigo-600 font-bold uppercase tracking-widest text-xs mb-2 block">
                    {{ $category->kind === 'prestations' ? __('messages.categories.kind_prestations') : __('messages.categories.kind_domestiques') }}
                </span>
                <h1 class="text-4xl font-black">{{ $category->name }}</h1>
                <p class="text-slate-500 mt-2">Découvrez les prestataires disponibles dans cette catégorie.</p>
            </div>
            <div class="text-slate-400 font-bold text-sm">
                {{ $providers->total() }} prestataire(s) trouvé(s)
            </div>
        </div>

        @if($providers->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Aucun prestataire pour le moment</h3>
                <p class="text-slate-500 mb-8">Nous n'avons pas encore de professionnels dans cette catégorie.</p>
                <a href="{{ route('register.provider') }}" class="bg-indigo-600 text-white px-6 sm:px-8 py-4 rounded-2xl font-black hover:bg-indigo-700 transition block text-center">Devenir le premier prestataire</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($providers as $provider)
                    @include('partials.provider-card', ['provider' => $provider])
                @endforeach
            </div>

            <div class="mt-12">
                {{ $providers->links() }}
            </div>
        @endif
    </main>

    <footer class="bg-white border-t border-slate-100 py-12 px-4 mt-20 text-center">
        <p class="text-slate-400 text-sm font-medium">&copy; 2026 Up Fiesta. Tous droits réservés.</p>
    </footer>
</body>
</html>