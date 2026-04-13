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
    <meta name="theme-color" content="#004aad">

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

    @include('partials.dark-mode-styles')
    @include('partials.head-scripts')
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 transition-colors duration-300">
    <!-- Navigation Header -->
    @include('partials.header')

    <!-- Search Bar Section -->
    <section class="bg-slate-50 dark:bg-slate-900 py-16 relative overflow-hidden border-b border-slate-200 dark:border-slate-800">
        <div class="absolute inset-0 bg-grid-slate-200/40 [mask-image:linear-gradient(0deg,transparent,black,transparent)] dark:bg-grid-slate-700/20 dark:[mask-image:linear-gradient(0deg,transparent,rgba(255,255,255,0.1),transparent)]"></div>
        <div class="max-w-6xl mx-auto px-4 relative z-10">
            <div class="text-center mb-10">
                <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-3 tracking-tight leading-tight">{{ __('messages.search.title') }}</h1>
                <p class="text-xl text-slate-700 dark:text-slate-300 font-semibold">{{ __('messages.search.subtitle') }}</p>
            </div>
            <form action="{{ route('search') }}" method="GET" class="flex flex-col gap-4">
                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1 flex items-center bg-white dark:bg-slate-800 px-6 py-4 rounded-2xl shadow-sm border-2 border-slate-300 dark:border-slate-700 focus-within:ring-4 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search.placeholder') }}" class="flex-1 ml-4 border-none outline-none focus:ring-0 text-slate-900 placeholder-slate-500 dark:placeholder-slate-400 font-semibold bg-transparent text-lg">
                    </div>

                    <div class="flex flex-col md:flex-row gap-3 flex-1 lg:flex-none lg:w-auto">
                        <!-- Category Filter -->
                        <div class="flex-1 md:w-64">
                            <select name="category" class="w-full h-full px-6 py-4 bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-slate-100 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold appearance-none shadow-sm cursor-pointer">
                                <option value="" class="text-slate-500 dark:text-slate-400">{{ __('messages.search.all_categories') }}</option>
                                @foreach($searchCategories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }} class="text-slate-900 dark:text-slate-100">
                                        {{ __('messages.categories_list.' . $category->slug) ?: $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City Filter -->
                        <div class="flex-1 md:w-64">
                            <select name="city" class="w-full h-full px-6 py-4 bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-slate-100 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold appearance-none shadow-sm cursor-pointer">
                                <option value="" class="text-slate-500 dark:text-slate-400">{{ __('messages.search.all_cities') }}</option>
                                @foreach($cities as $city)
                                    @if(Str::contains(Str::lower($city->name), 'lomé'))
                                        <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }} class="text-slate-900 dark:text-slate-100">
                                            {{ __('messages.cities_list.' . \Illuminate\Support\Str::slug($city->name)) ?: $city->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-black transition-all flex items-center justify-center gap-3 shadow-lg shadow-indigo-200 dark:shadow-none hover:-translate-y-0.5 active:translate-y-0 whitespace-nowrap text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        {{ __('messages.search.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Results Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <!-- Results Header -->
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-8 mb-14">
            <div class="flex-1">
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-3 leading-tight">
                    @if(request('q') || request('category') || request('city'))
                        {{ __('messages.search.results_for') }}
                        <br>
                        <span class="text-indigo-600 dark:text-indigo-400">
                            "{{ request('q') ?: (request('category') ? ($searchCategories->where('id', request('category'))->first()->name ?? '') : (request('city') ? ($cities->where('id', request('city'))->first()->name ?? '') : __('messages.filters.all'))) }}"
                        </span>
                    @else
                        {{ __('messages.search.results') }}
                    @endif
                </h2>
                <p class="text-lg text-slate-800 dark:text-slate-300 font-semibold">
                    @if($providers->total() === 1)
                        {{ __('messages.search.count_single') }}
                    @else
                        <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $providers->total() }}</span> {{ __('messages.search.count_multiple', ['count' => $providers->total()]) }}
                    @endif
                </p>
            </div>

            <!-- Active Filters Tags -->
            @if(request('q') || request('category') || request('city'))
                <div class="flex flex-wrap gap-2 self-start lg:mt-2">
                    @if(request('q'))
                        <span class="inline-flex items-center gap-2 bg-blue-100 dark:bg-white text-slate-900 dark:text-blue-100 px-4 py-2.5 rounded-xl text-xs font-bold border border-blue-300 dark:border-blue-600 shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-search text-blue-600 dark:text-blue-400"></i>
                            <span>{{ request('q') }}</span>
                            <a href="{{ route('search', request()->except(['q'])) }}" class="ml-1 hover:text-rose-600 dark:hover:text-rose-400 transition-colors font-black">×</a>
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="inline-flex items-center gap-2 bg-green-20 text-slate-900 px-4 py-2.5 rounded-xl text-xs font-bold border border-green-300 dark:border-green-600 shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-tag text-green-600 dark:text-green-400"></i>
                            <span>{{ $searchCategories->where('id', request('category'))->first()->name ?? '' }}</span>
                            <a href="{{ route('search', request()->except(['category'])) }}" class="ml-1 hover:text-rose-600 dark:hover:text-rose-400 transition-colors font-black">×</a>
                        </span>
                    @endif
                    @if(request('city'))
                        <span class="inline-flex items-center gap-2 bg-purple-100 dark:bg-purple-900/30 text-slate-900 dark:text-purple-100 px-4 py-2.5 rounded-xl text-xs font-bold border border-purple-300 dark:border-purple-600 shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-map-marker-alt text-purple-600 dark:text-purple-400"></i>
                            <span>{{ $cities->where('id', request('city'))->first()->name ?? '' }}</span>
                            <a href="{{ route('search', request()->except(['city'])) }}" class="ml-1 hover:text-rose-600 dark:hover:text-rose-400 transition-colors font-black">×</a>
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
            <div class="relative py-20 px-8 overflow-hidden bg-white dark:bg-slate-800 rounded-3xl border-2 border-slate-200 dark:border-slate-700 shadow-lg">
                <div class="absolute top-0 right-0 -mt-32 -mr-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-32 -ml-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 max-w-xl mx-auto text-center">
                    <div class="w-28 h-28 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-8 border-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h2 class="text-4xl font-black text-slate-1000 dark:text-white mb-4 tracking-tight">{{ __('messages.search.empty_title') }}</h2>
                    <p class="text-lg text-slate-900 dark:text-slate-400 mb-12 leading-relaxed max-w-lg mx-auto">{{ __('messages.search.empty_subtitle') }}</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-2xl font-black transition-all shadow-lg shadow-indigo-200 dark:shadow-none transform hover:-translate-y-0.5 active:translate-y-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ __('messages.search.back_home') }}
                        </a>
                        <a href="{{ route('search') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-300 px-10 py-5 rounded-2xl font-black border-2 border-slate-300 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all shadow-md transform hover:-translate-y-0.5 active:translate-y-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('messages.search.clear_filters') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    @include('partials.footer')
</body>
</html>
