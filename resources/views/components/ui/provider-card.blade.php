{{-- Provider Profile Card Component --}}
@props([
    'provider',
    'showStats' => true,
])

<x-ui.card variant="gradient" padding="lg" class="text-center h-full">
    <!-- Avatar -->
    <div class="mb-4 flex justify-center">
        <div class="relative w-20 h-20 rounded-full overflow-hidden border-4 border-primary-500 shadow-medium">
            @if (isset($provider['avatar']))
                <img src="{{ $provider['avatar'] }}" alt="{{ $provider['name'] ?? 'Provider' }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($provider['name'] ?? 'P', 0, 1)) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Name & Title -->
    <h3 class="text-2xl font-bold text-white mb-1">{{ $provider['name'] ?? 'Provider' }}</h3>
    <p class="text-primary-100 text-sm mb-4">{{ $provider['title'] ?? 'Professional' }}</p>

    <!-- Description -->
    @if (isset($provider['description']))
        <p class="text-primary-50 text-sm mb-6 line-clamp-3">{{ $provider['description'] }}</p>
    @endif

    <!-- Stats -->
    @if ($showStats)
        <div class="grid grid-cols-3 gap-4 py-6 border-y border-primary-400">
            @if (isset($provider['rating']))
                <div>
                    <p class="text-xl font-bold text-white">{{ $provider['rating'] }}</p>
                    <p class="text-xs text-primary-100">Rating</p>
                </div>
            @endif
            @if (isset($provider['reviews']))
                <div>
                    <p class="text-xl font-bold text-white">{{ $provider['reviews'] }}</p>
                    <p class="text-xs text-primary-100">Avis</p>
                </div>
            @endif
            @if (isset($provider['completed']))
                <div>
                    <p class="text-xl font-bold text-white">{{ $provider['completed'] }}</p>
                    <p class="text-xs text-primary-100">Complétés</p>
                </div>
            @endif
        </div>
    @endif

    <!-- CTA -->
    @isset($cta)
        <div class="mt-6">
            {{ $cta }}
        </div>
    @endisset
</x-ui.card>
