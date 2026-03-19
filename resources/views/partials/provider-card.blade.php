<div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-xl hover:shadow-slate-200 dark:hover:shadow-slate-900 transition-all overflow-hidden flex flex-col h-full">
    <!-- Image/Logo -->
    <div class="h-48 bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
        @if($provider->logo)
            <img src="{{ asset('storage/' . $provider->logo) }}" alt="{{ $provider->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center bg-indigo-50 dark:bg-slate-700 text-indigo-300 dark:text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        @endif
        @if($provider->is_verified)
            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur p-1.5 rounded-full shadow-sm" title="Vérifié">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    <div class="p-8 flex-1 flex flex-col">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                @if($provider->categories->isNotEmpty())
                    {{ $provider->categories->pluck('name')->implode(', ') }}
                @else
                    {{ $provider->category->name ?? 'Sans catégorie' }}
                @endif
            </span>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-bold">{{ $provider->city->name ?? 'Togo' }}</span>
        </div>

        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">{{ $provider->name }}</h3>
            <div class="text-indigo-600 dark:text-indigo-400 font-black text-lg">
                {{ number_format($provider->base_price, 0, ',', ' ') }}<span class="text-xs ml-1">XOF</span>
            </div>
        </div>
        
        <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-6 flex-1">{{ $provider->description }}</p>
        
        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50 dark:border-slate-700">
            <div class="flex gap-2">
                <button onclick="openBookingModal({{ $provider->id }}, '{{ addslashes($provider->name) }})" class="p-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition" title="Réserver">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                @php
                    $contactUrl = route('login');
                    if (auth()->check()) {
                        // Redirection vers la messagerie avec Up Fiesta (Admin ID 1) pas le prestataire
                        $contactUrl = route('messages.show', ['user' => 1, 'needs_provider' => $provider->id]);
                    }
                @endphp
                <a href="{{ $contactUrl }}" class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition" title="Exprimer mes besoins">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </a>
            </div>
            <a href="{{ route('providers.show', $provider->id) }}" class="text-sm font-bold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition">Voir profil</a>
        </div>
    </div>
</div>
