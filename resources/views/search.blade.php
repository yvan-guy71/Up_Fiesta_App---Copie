<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Résultats de recherche - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">

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
        html { scroll-behavior: smooth; }
        /* Light mode placeholders */
        input::placeholder { color: #4b5563; }
        select { color: #374151; }
        /* Dark mode */
        html.dark body { background-color: #020617; color: #e5e7eb; }
        html.dark header { background-color: rgba(15, 23, 42, 0.95); border-bottom-color: #1f2937; }
        html.dark section { background-color: #111827; }
        html.dark .bg-white { background-color: #1a1f2e; border-color: #2d3748; }
        html.dark .bg-slate-50 { background-color: #020617; }
        html.dark .border-slate-100, html.dark .border-slate-200 { border-color: #2d3748; }
        html.dark .text-slate-500, html.dark .text-slate-600, html.dark .text-slate-700, html.dark .text-slate-900 { color: #e5e7eb; }
        html.dark input[type="text"], html.dark select { background-color: #111827; color: #e5e7eb; border-color: #2d3748; }
        html.dark input::placeholder { color: #9ca3af; }
        html.dark select option { background-color: #1a1f2e; color: #e5e7eb; }
        html.dark select option:checked { background-color: #4f46e5; color: #ffffff; }
        html.dark input:focus { background-color: #1a1f2e; border-color: #4f46e5; }
        html.dark select:focus { background-color: #1a1f2e; border-color: #4f46e5; }
        html.dark footer { background-color: #020617; border-color: #1f2937; }
        html.dark .bg-indigo-100 { background-color: #312e81; }
        html.dark .text-indigo-700 { color: #a5b4fc; }
        html.dark .bg-slate-100 { background-color: #1f2937; }
        html.dark .hover\:bg-slate-200:hover { background-color: #2d3748; }
        * { transition-property: background-color, border-color, color; transition-duration: 200ms; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <!-- Navigation Header -->
    @include('partials.header')

    <!-- Search Bar Section -->
    <section class="bg-gradient-to-b from-indigo-50 to-white dark:from-slate-800 dark:to-slate-900 border-b border-slate-100 dark:border-slate-700 py-8">
        <div class="max-w-5xl mx-auto px-4">
            <form action="{{ route('search') }}" method="GET" class="flex flex-col gap-4">
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1 flex items-center bg-white dark:bg-slate-800 px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:ring-indigo-900 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Chercher..." class="flex-1 ml-3 bg-transparent border-none outline-none focus:ring-0 text-slate-900 dark:text-white placeholder-slate-600 dark:placeholder-slate-300">
                    </div>

                    <!-- Category Filter -->
                    <div class="xl:w-48">
                        <select name="category" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition">
                            <option value="" class="text-slate-700 dark:text-slate-400">Toutes catégories</option>
                            @foreach($searchCategories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City Filter -->
                    <div class="xl:w-48">
                        <select name="city" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition">
                            <option value="" class="text-slate-700 dark:text-slate-400">Toutes les villes</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Chercher
                    </button>
                </div>

                <!-- Kind Filter -->
                <div class="flex gap-3">
                    <a href="{{ route('search', request()->except(['kind'])) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ !request('kind') ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        Tous types
                    </a>
                    <a href="{{ route('search', array_merge(request()->all(), ['kind' => 'prestations'])) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ request('kind') === 'prestations' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        Prestations
                    </a>
                    <a href="{{ route('search', array_merge(request()->all(), ['kind' => 'domestiques'])) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ request('kind') === 'domestiques' ? 'bg-green-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        Domestiques
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Results Section -->
    <main class="max-w-7xl mx-auto px-4 py-16">
        <!-- Results Header -->
        <div class="mb-12">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900 dark:text-white">
                        @if($selectedCategory)
                            {{ $selectedCategory->name }}
                        @elseif(request('q'))
                            Résultats pour "{{ request('q') }}"
                        @else
                            Résultats de recherche
                        @endif
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">
                        @if($totalResults == 0)
                            Aucun résultat trouvé
                        @elseif($totalResults == 1)
                            1 prestataire trouvé
                        @else
                            {{ $totalResults }} prestataires trouvés
                        @endif
                    </p>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if(request()->anyFilled(['q', 'category', 'city', 'kind']))
            <div class="mt-6 flex flex-wrap gap-2">
                @if(request('q'))
                    <span class="inline-flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full text-sm">
                        <span>Recherche: "{{ request('q') }}"</span>
                        <a href="{{ route('search', request()->except(['q'])) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">×</a>
                    </span>
                @endif
                @if($selectedCategory)
                    <span class="inline-flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full text-sm">
                        <span>Catégorie: {{ $selectedCategory->name }}</span>
                        <a href="{{ route('search', request()->except(['category'])) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">×</a>
                    </span>
                @endif
                @if($selectedCity)
                    <span class="inline-flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full text-sm">
                        <span>Ville: {{ $selectedCity->name }}</span>
                        <a href="{{ route('search', request()->except(['city'])) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">×</a>
                    </span>
                @endif
                @if(request('kind'))
                    <span class="inline-flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full text-sm">
                        <span>Type: {{ request('kind') === 'prestations' ? 'Prestations' : 'Domestiques' }}</span>
                        <a href="{{ route('search', request()->except(['kind'])) }}" class="hover:text-indigo-900 dark:hover:text-indigo-200">×</a>
                    </span>
                @endif
            </div>
            @endif
        </div>

        <!-- Results Grid -->
        @if($providers->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($providers as $provider)
                    @include('partials.provider-card', ['provider' => $provider])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $providers->links() }}
            </div>
        @else
            <!-- No Results Message -->
            <div class="text-center py-20 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700">
                <div class="w-24 h-24 bg-white dark:bg-slate-700 text-slate-300 dark:text-slate-600 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-200 dark:border-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Aucun prestataire trouvé</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-6">Essayez d'ajuster vos critères de recherche</p>
                <a href="{{ route('home') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    Retour à l'accueil
                </a>
            </div>
        @endif
    </main>

    <!-- Footer -->
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
    </script>
</body>
</html>
