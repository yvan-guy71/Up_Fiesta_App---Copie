{{-- Spinner/Loading Component --}}
@props([
    'size' => 'md',
    'color' => 'primary',
])

@php
$sizes = [
    'xs' => ['outer' => 'w-4 h-4', 'inner' => 'border-2'],
    'sm' => ['outer' => 'w-6 h-6', 'inner' => 'border-2'],
    'md' => ['outer' => 'w-8 h-8', 'inner' => 'border-3'],
    'lg' => ['outer' => 'w-12 h-12', 'inner' => 'border-4'],
    'xl' => ['outer' => 'w-16 h-16', 'inner' => 'border-4'],
];

$colors = [
    'primary' => 'border-primary-300 border-t-primary-600',
    'secondary' => 'border-secondary-300 border-t-secondary-600',
    'success' => 'border-success-300 border-t-success-600',
    'warning' => 'border-warning-300 border-t-warning-600',
    'danger' => 'border-danger-300 border-t-danger-600',
    'white' => 'border-white/30 border-t-white',
];

$sizeConfig = $sizes[$size] ?? $sizes['md'];
$colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div {{ $attributes->merge(['class' => "{$sizeConfig['outer']} animate-spin"]) }}>
    <div class="w-full h-full rounded-full border {{ $sizeConfig['inner'] }} {{ $colorClass }}"></div>
</div>
