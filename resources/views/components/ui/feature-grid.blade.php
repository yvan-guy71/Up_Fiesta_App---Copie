{{-- Feature Grid Component --}}
@props([
    'features' => [],
])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach ($features as $index => $feature)
        <div class="animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s">
            <x-ui.card variant="flat" padding="lg" class="h-full hover:shadow-medium transition-all duration-300">
                <!-- Icon -->
                @if (isset($feature['icon']))
                    <div class="mb-4 p-3 bg-primary-100 rounded-lg w-fit">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $feature['icon'] !!}
                        </svg>
                    </div>
                @endif

                <!-- Title -->
                <h3 class="text-xl font-bold text-secondary-900 mb-2">{{ $feature['title'] ?? '' }}</h3>

                <!-- Description -->
                <p class="text-secondary-600 leading-relaxed">{{ $feature['description'] ?? '' }}</p>
            </x-ui.card>
        </div>
    @endforeach
</div>
