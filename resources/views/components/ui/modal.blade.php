{{-- Modal Component Professionnel --}}
@props([
    'title' => null,
    'size' => 'md',
    'closeButton' => true,
])

@php
$sizes = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    'full' => 'max-w-full',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div x-data="{ open: false }" 
    class="relative">
    
    <!-- Trigger Slot -->
    <div @click="open = true" class="cursor-pointer">
        {{ $trigger ?? '' }}
    </div>

    <!-- Modal Backdrop -->
    <div x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm" style="display: none"
        x-cloak>
    </div>

    <!-- Modal Content -->
    <div x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none"
        x-cloak>
        
        <div class="w-full {{ $sizeClass }} bg-white rounded-2xl shadow-strong animate-scale-bounce">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-secondary-100">
                @if ($title)
                    <h2 class="text-2xl font-bold text-secondary-900">{{ $title }}</h2>
                @endif
                
                @if ($closeButton)
                    <button @click="open = false"
                        class="text-secondary-400 hover:text-secondary-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Body -->
            <div class="p-6">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @isset($footer)
                <div class="flex gap-3 justify-end p-6 border-t border-secondary-100">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
