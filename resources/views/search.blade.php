<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.search.results') }} - Up Fiesta</title>
    
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
        input::placeholder { color: #94a3b8; }
        select { color: #374151; }
        /* Dark mode */
        html.dark body { background-color: #020617; color: #f1f5f9; }
        html.dark header { background-color: rgba(15, 23, 42, 0.95); border-bottom-color: #1f2937; }
        html.dark section { background-color: transparent; }
        html.dark .bg-white { background-color: #1e293b; border-color: #334155; }
        html.dark .bg-slate-50 { background-color: #020617; }
        html.dark .border-slate-100, html.dark .border-slate-200 { border-color: #334155; }
        html.dark .text-slate-500, html.dark .text-slate-600, html.dark .text-slate-700, html.dark .text-slate-900 { color: #cbd5e1; }
        html.dark input[type="text"], html.dark select { background-color: #0f172a; color: #f1f5f9; border-color: #334155; }
        html.dark input::placeholder { color: #64748b; }
        html.dark select option { background-color: #1e293b; color: #f1f5f9; }
        html.dark select option:checked { background-color: #4f46e5; color: #ffffff; }
        html.dark input:focus, html.dark select:focus { border-color: #6366f1; ring-color: #6366f1; }
        html.dark footer { background-color: #020617; border-color: #1f2937; }
        html.dark .bg-indigo-100 { background-color: #312e81; }
        html.dark .text-indigo-700 { color: #a5b4fc; }
        html.dark .bg-slate-100 { background-color: #1f2937; }
        html.dark .hover\:bg-slate-200:hover { background-color: #334155; }
        * { transition-property: background-color, border-color, color; transition-duration: 200ms; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 transition-colors duration-300">
    <!-- Navigation Header -->
    @include('partials.header')

    <!-- Search Bar Section -->
    <section class="py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-slate-200/50 [mask-image:linear-gradient(0deg,transparent,black,transparent)] dark:bg-grid-slate-700/20 dark:[mask-image:linear-gradient(0deg,transparent,rgba(255,255,255,0.1),transparent)]"></div>
        <div class="max-w-5xl mx-auto px-4 relative z-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">{{ __('messages.search.title') }}</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">{{ __('messages.search.subtitle') }}</p>
            </div>
            <form action="{{ route('search') }}" method="GET" class="flex flex-col gap-6">
                <div class="flex flex-col xl:flex-row gap-4 p-2">
                    <!-- Search Input -->
                    <div class="flex-1 flex items-center bg-white dark:bg-slate-800 px-6 py-4 rounded-[1.5rem] shadow-sm border border-slate-200/50 dark:border-slate-700/50 focus-within:ring-4 focus-within:ring-indigo-500/10 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search.placeholder') }}" class="flex-1 ml-3 bg-transparent border-none outline-none focus:ring-0 text-slate-900 dark:text-white placeholder-slate-400 font-bold">
                    </div>

                    <div class="flex flex-col md:flex-row gap-2 flex-1 xl:flex-none">
                        <!-- Category Filter -->
                        <div class="xl:w-56">
                            <select name="category" class="w-full h-full px-6 py-4 bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 rounded-[1.5rem] text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold appearance-none shadow-sm">
                                <option value="" class="text-slate-500">{{ __('messages.search.all_categories') }}</option>
                                @foreach($searchCategories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ __('messages.categories_list.' . $category->slug) ?: $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City Filter -->
                        <div class="xl:w-56">
                            <select name="city" class="w-full h-full px-6 py-4 bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 rounded-[1.5rem] text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold appearance-none shadow-sm">
                                <option value="" class="text-slate-500">{{ __('messages.search.all_cities') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                        {{ __('messages.cities_list.' . \Illuminate\Support\Str::slug($city->name)) ?: $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-[1.5rem] font-black transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-200 dark:shadow-none hover:-translate-y-0.5 active:translate-y-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        {{ __('messages.search.submit') }}
                    </button>
                </div>

                <!-- Kind Filter -->
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('search', request()->except(['kind'])) }}" 
                       class="px-6 py-2.5 rounded-full text-sm font-black transition-all {{ !request('kind') ? 'bg-slate-900 text-white shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-500 shadow-sm' }}">
                        {{ __('messages.search.all_types') }}
                    </a>
                    <a href="{{ route('search', array_merge(request()->all(), ['kind' => 'prestations'])) }}" 
                       class="px-6 py-2.5 rounded-full text-sm font-black transition-all {{ request('kind') === 'prestations' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-400 shadow-sm' }}">
                        {{ __('messages.categories.kind_prestations') }}
                    </a>
                    <a href="{{ route('search', array_merge(request()->all(), ['kind' => 'domestiques'])) }}" 
                       class="px-6 py-2.5 rounded-full text-sm font-black transition-all {{ request('kind') === 'domestiques' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-400 shadow-sm' }}">
                        {{ __('messages.categories.kind_domestiques') }}
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Results Section -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Results Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    @if(request('q') || request('category') || request('city') || request('kind'))
                        {{ __('messages.search.results_for') }} 
                        <span class="text-indigo-600 dark:text-indigo-400">
                            "{{ request('q') ?: (request('category') ? (__('messages.categories_list.' . $searchCategories->where('id', request('category'))->first()->slug) ?: $searchCategories->where('id', request('category'))->first()->name) : (request('kind') ? __('messages.categories.kind_' . request('kind')) : __('messages.filters.all'))) }}"
                        </span>
                    @else
                        {{ __('messages.search.results') }}
                    @endif
                </h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-bold">
                    @if($providers->total() === 1)
                        {{ __('messages.search.count_single') }}
                    @else
                        {{ __('messages.search.count_multiple', ['count' => $providers->total()]) }}
                    @endif
                </p>
            </div>

            <!-- Active Filters -->
            <div class="flex flex-wrap gap-2">
                @if(request('q'))
                    <span class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl text-xs font-black border border-slate-200 dark:border-slate-700 shadow-sm">
                        <span>{{ __('messages.search.filter_query') }} {{ request('q') }}</span>
                        <a href="{{ route('search', request()->except(['q'])) }}" class="hover:text-rose-500 transition-colors">×</a>
                    </span>
                @endif
                @if(request('category'))
                    <span class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl text-xs font-black border border-slate-200 dark:border-slate-700 shadow-sm">
                        <span>{{ __('messages.search.filter_category') }} {{ $searchCategories->where('id', request('category'))->first()->name }}</span>
                        <a href="{{ route('search', request()->except(['category'])) }}" class="hover:text-rose-500 transition-colors">×</a>
                    </span>
                @endif
                @if(request('city'))
                    <span class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl text-xs font-black border border-slate-200 dark:border-slate-700 shadow-sm">
                        <span>{{ __('messages.search.filter_city') }} {{ $cities->where('id', request('city'))->first()->name }}</span>
                        <a href="{{ route('search', request()->except(['city'])) }}" class="hover:text-rose-500 transition-colors">×</a>
                    </span>
                @endif
                @if(request('kind'))
                    <span class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl text-xs font-black border border-slate-200 dark:border-slate-700 shadow-sm">
                        <span>{{ __('messages.search.filter_type') }} {{ request('kind') === 'prestations' ? __('messages.categories.kind_prestations') : __('messages.categories.kind_domestiques') }}</span>
                        <a href="{{ route('search', request()->except(['kind'])) }}" class="hover:text-rose-500 transition-colors">×</a>
                    </span>
                @endif
            </div>
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
            <div class="relative py-24 px-6 overflow-hidden bg-white dark:bg-slate-800/50 rounded-[3.5rem] border border-slate-200/60 dark:border-slate-700/50 shadow-sm">
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 max-w-lg mx-auto text-center">
                    <div class="w-24 h-24 bg-white dark:bg-slate-800 text-indigo-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-indigo-100 dark:shadow-none border border-slate-100 dark:border-slate-700 rotate-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-4 tracking-tight">{{ __('messages.search.empty_title') }}</h2>
                    <p class="text-slate-500 dark:text-slate-400 mb-10 text-lg leading-relaxed">{{ __('messages.search.empty_subtitle') }}</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-black transition shadow-lg shadow-indigo-200 dark:shadow-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ __('messages.search.back_home') }}
                        </a>
                        <a href="{{ route('search') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-8 py-4 rounded-2xl font-black border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                            {{ __('messages.search.clear_filters') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

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
