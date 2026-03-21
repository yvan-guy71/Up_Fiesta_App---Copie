<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md hover:shadow-2xl hover:shadow-slate-300/50 dark:hover:shadow-slate-950/50 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden flex flex-col h-full group">
    
    <!-- Image/Logo Section -->
    <div class="relative h-52 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 overflow-hidden">
        @if($provider->logo)
            <img src="{{ asset('storage/' . $provider->logo) }}" alt="{{ $provider->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-slate-700 dark:to-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-indigo-200 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        @endif
        
        <!-- Verification Badge -->
        @if($provider->verification_status === 'approved')
            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md p-2 rounded-full shadow-lg ring-2 ring-green-100 dark:ring-green-900/50 animate-pulse-slow" title="Prestataire Vérifié">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
        @elseif($provider->verification_status === 'pending')
            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md p-2 rounded-full shadow-lg ring-2 ring-amber-100 dark:ring-amber-900/50" title="En attente de vérification">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400 animate-spin-slow" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif

        <!-- overlay hover -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <!-- Content Section -->
    <div class="p-6 flex-1 flex flex-col bg-white dark:bg-slate-800">
        
        <!-- Category & Location -->
        <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-2">
                <span class="inline-block px-3 py-1 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-semibold tracking-wide">
                    @if($provider->categories->isNotEmpty())
                        {{ $provider->categories->first()->name }}
                    @else
                        {{ $provider->category->name ?? 'Services' }}
                    @endif
                </span>
            </div>
            <div class="flex items-center gap-1 text-slate-600 dark:text-slate-400 text-xs font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <span>{{ $provider->city->name ?? 'Togo' }}</span>
            </div>
        </div>

        <!-- Name & Price -->
        <div class="mb-3">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-1 line-clamp-2">
                {{ $provider->name }}
            </h3>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                    {{ number_format($provider->base_price, 0, ',', ' ') }}
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">XOF/mission</span>
            </div>
        </div>
        
        <!-- Description -->
        <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed line-clamp-2 mb-4 flex-1">
            {{ $provider->description }}
        </p>

        <!-- Experience & Stats -->
        <div class="flex items-center gap-3 mb-5 pb-4 border-t border-slate-200 dark:border-slate-700">
            @if($provider->years_of_experience)
                <div class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $provider->years_of_experience }} ans</span>
                </div>
            @endif
            <div class="flex-1"></div>
            @if($provider->is_verified)
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600 dark:text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs font-semibold text-green-700 dark:text-green-400">Vérifié</span>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
            <button onclick="openBookingModal({{ $provider->id }}, '{{ addslashes($provider->name) }})" 
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-lg transition-all duration-300 shadow-sm hover:shadow-lg active:scale-95"
                title="Réserver ce prestataire">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Réserver</span>
            </button>

            @php
                $contactUrl = route('login');
                if (auth()->check()) {
                    $contactUrl = route('messages.show', ['user' => 1, 'needs_provider' => $provider->id]);
                }
            @endphp
            <a href="{{ $contactUrl }}" 
                class="p-2.5 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-all duration-300 ring-2 ring-transparent hover:ring-indigo-200 dark:hover:ring-indigo-800"
                title="Exprimer mes besoins">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </a>

            <a href="{{ route('providers.show', $provider->id) }}" 
                class="p-2.5 text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-all duration-300 ring-2 ring-transparent hover:ring-slate-300 dark:hover:ring-slate-600"
                title="Voir le profil complet">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
        </div>
    </div>
</div>