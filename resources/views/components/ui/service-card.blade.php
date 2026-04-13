{{-- Service Card Component Professionnel --}}
@props([
    'service',
    'showRating' => true,
    'showPrice' => true,
    'clickable' => true,
])

<x-ui.card variant="elevated" padding="md" :hoverable="$clickable" class="h-full group">
    <!-- Image -->
    <div class="relative overflow-hidden rounded-xl mb-4 bg-secondary-100 h-48">
        @if (isset($service['image']))
            <img src="{{ $service['image'] }}" alt="{{ $service['name'] ?? 'Service' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
        
        <!-- Badge -->
        @if (isset($service['badge']))
            <div class="absolute top-3 right-3">
                <x-ui.badge variant="primary" size="sm">{{ $service['badge'] }}</x-ui.badge>
            </div>
        @endif
    </div>

    <!-- Content -->
    <div class="space-y-3">
        <!-- Category -->
        @if (isset($service['category']))
            <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider">{{ $service['category'] }}</p>
        @endif

        <!-- Title -->
        <h3 class="text-lg font-bold text-secondary-900 line-clamp-2">{{ $service['name'] ?? 'Service' }}</h3>

        <!-- Description -->
        @if (isset($service['description']))
            <p class="text-sm text-secondary-600 line-clamp-2">{{ $service['description'] }}</p>
        @endif

        <!-- Rating -->
        @if ($showRating && isset($service['rating']))
            <div class="flex items-center gap-2 pt-2">
                <div class="flex items-center gap-1">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 {{ $i < floor($service['rating']) ? 'text-warning-400' : 'text-secondary-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    @endfor
                </div>
                <span class="text-sm font-semibold text-secondary-900">{{ $service['rating'] }}</span>
                @if (isset($service['reviews']))
                    <span class="text-sm text-secondary-500">({{ $service['reviews'] }})</span>
                @endif
            </div>
        @endif

        <!-- Price -->
        @if ($showPrice && isset($service['price']))
            <div class="flex items-baseline gap-2 pt-4 border-t border-secondary-100">
                <span class="text-2xl font-bold text-primary-600">{{ $service['price'] }}</span>
                @if (isset($service['original_price']))
                    <span class="text-sm line-through text-secondary-400">{{ $service['original_price'] }}</span>
                @endif
                @if (isset($service['unit']))
                    <span class="text-sm text-secondary-600">/{{ $service['unit'] }}</span>
                @endif
            </div>
        @endif
    </div>

    <!-- CTA -->
    @isset($cta)
        <div class="mt-4 pt-4 border-t border-secondary-100">
            {{ $cta }}
        </div>
    @endisset
</x-ui.card>
