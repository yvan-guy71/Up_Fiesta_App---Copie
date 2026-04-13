{{-- Button Component Professionnel --}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => 'lg',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'type' => 'button',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 dark:from-blue-600 dark:to-blue-700 focus:ring-blue-500 shadow-md hover:shadow-lg shadow-blue-500/20 dark:shadow-blue-900/30 hover:scale-105',
    'secondary' => 'bg-slate-600 text-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 focus:ring-slate-500 shadow-sm hover:shadow-soft',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 shadow-md hover:shadow-medium',
    'warning' => 'bg-amber-600 text-white hover:bg-amber-700 focus:ring-amber-500 shadow-md hover:shadow-medium',
    'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500 shadow-md hover:shadow-medium',
    'info' => 'bg-cyan-600 text-white hover:bg-cyan-700 focus:ring-cyan-500 shadow-md hover:shadow-medium',
    'outline-primary' => 'border-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 focus:ring-blue-500',
    'outline-secondary' => 'border-2 border-slate-600 text-slate-600 dark:text-slate-400 dark:border-slate-400 hover:bg-slate-50 dark:hover:bg-slate-500/10 focus:ring-slate-500',
    'ghost' => 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 focus:ring-slate-500',
    'ghost-primary' => 'text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 focus:ring-blue-500',
];

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs gap-1',
    'sm' => 'px-3 py-2 text-sm gap-1.5',
    'md' => 'px-4 py-2.5 text-sm font-medium gap-2',
    'lg' => 'px-6 py-3 text-base gap-2.5',
    'xl' => 'px-8 py-4 text-lg gap-3',
];

$roundedClasses = [
    'none' => 'rounded-none',
    'sm' => 'rounded-sm',
    'md' => 'rounded-md',
    'lg' => 'rounded-lg',
    'xl' => 'rounded-xl',
    '2xl' => 'rounded-2xl',
    'full' => 'rounded-full',
];

$variantClass = $variants[$variant] ?? $variants['primary'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
$roundedClass = $roundedClasses[$rounded] ?? $roundedClasses['lg'];
$classes = "$baseClasses $variantClass $sizeClass $roundedClass";
@endphp

<button
    type="{{ $type }}"
    {{ $disabled || $loading ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if ($icon && $iconPosition === 'left')
        <svg class="w-5 h-5 {{ $loading ? 'animate-spin' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif

    @if ($loading)
        <span class="inline-block">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    @endif

    <span>{{ $slot }}</span>

    @if ($icon && $iconPosition === 'right')
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif
</button>
