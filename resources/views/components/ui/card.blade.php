{{-- Card Component Professionnel --}}
@props([
    'variant' => 'elevated',
    'padding' => 'md',
    'hoverable' => false,
    'interactive' => false,
])

@php
$baseClasses = 'bg-white transition-all duration-300 rounded-2xl';

$variants = [
    'elevated' => 'shadow-md dark:shadow-lg dark:shadow-blue-900/30',
    'flat' => 'shadow-sm border border-slate-200 dark:border-blue-500/20',
    'outlined' => 'border-2 border-slate-300 dark:border-blue-500/30',
    'gradient' => 'bg-gradient-to-br from-blue-50 dark:from-blue-950 to-slate-50 dark:to-slate-900 shadow-md border border-blue-100 dark:border-blue-500/20',
    'glass' => 'backdrop-blur-md bg-white/90 dark:bg-slate-900/90 border border-white/20 dark:border-blue-500/20 shadow-lg',
];

$paddings = [
    'xs' => 'p-2',
    'sm' => 'p-3',
    'md' => 'p-6',
    'lg' => 'p-8',
    'xl' => 'p-10',
    'none' => 'p-0',
];

$variantClass = $variants[$variant] ?? $variants['elevated'];
$paddingClass = $paddings[$padding] ?? $paddings['md'];

$hoverableClass = $hoverable ? 'hover:shadow-medium hover:scale-[1.02] cursor-pointer' : '';
$interactiveClass = $interactive ? 'hover:shadow-strong -translate-y-1 cursor-pointer' : '';

$classes = "$baseClasses $variantClass $paddingClass $hoverableClass $interactiveClass";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
