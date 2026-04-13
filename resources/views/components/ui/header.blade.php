{{-- Header Component Professionnel --}}
@props([
    'logo' => null,
    'sticky' => true,
])

@php
$stickyClass = $sticky ? 'sticky top-0 z-40' : '';
@endphp

<nav {{ $attributes->merge(['class' => "$stickyClass bg-white shadow-soft border-b border-secondary-100 transition-all duration-300"]) }}>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                @if ($logo)
                    <a href="/" class="flex items-center hover:scale-110 transition-transform duration-200">
                        <img src="{{ $logo }}" alt="Logo" class="h-10 w-auto">
                    </a>
                @else
                    <a href="/" class="text-2xl font-bold text-primary-600 hover:scale-110 transition-transform duration-200">
                        Up Fiesta
                    </a>
                @endif
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-1">
                {{ $links ?? '' }}
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                {{ $actions ?? '' }}
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                {{ $mobile ?? '' }}
            </div>
        </div>
    </div>
</nav>
