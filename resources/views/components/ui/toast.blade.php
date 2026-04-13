{{-- Notification Component (Toast) --}}
@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'duration' => 5000,
    'position' => 'top-right',
])

@php
$positions = [
    'top-left' => 'top-4 left-4',
    'top-center' => 'top-4 left-1/2 -translate-x-1/2',
    'top-right' => 'top-4 right-4',
    'bottom-left' => 'bottom-4 left-4',
    'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2',
    'bottom-right' => 'bottom-4 right-4',
];

$colors = [
    'success' => 'bg-success-50 border-success-200 text-success-800',
    'error' => 'bg-danger-50 border-danger-200 text-danger-800',
    'warning' => 'bg-warning-50 border-warning-200 text-warning-800',
    'info' => 'bg-info-50 border-info-200 text-info-800',
];

$icons = [
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l4-4m0 0l-4-4m4 4l4 4m-4-4l-4-4"></path>',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0-6a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"></path>',
    'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
];

$positionClass = $positions[$position] ?? $positions['top-right'];
$colorClass = $colors[$type] ?? $colors['info'];
$icon = $icons[$type] ?? $icons['info'];
@endphp

<div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, {{ $duration }})"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90 {{ strpos($position, 'top') ? '-translate-y-4' : 'translate-y-4' }}"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90 {{ strpos($position, 'top') ? '-translate-y-4' : 'translate-y-4' }}"
    class="fixed {{ $positionClass }} z-50 pointer-events-auto"
    x-cloak>
    
    <div class="border {{ $colorClass }} rounded-lg p-4 shadow-medium max-w-md flex gap-3">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        </div>
        <div class="flex-1">
            @if ($title)
                <h3 class="font-semibold">{{ $title }}</h3>
            @endif
            @if ($message)
                <p class="text-sm">{{ $message }}</p>
            @endif
        </div>
        <button @click="show = false" class="flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
