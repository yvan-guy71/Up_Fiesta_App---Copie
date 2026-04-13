{{-- Badge Component Professionnel --}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => 'full',
    'dotted' => false,
])

@php
$baseClasses = 'inline-flex items-center font-medium transition-all duration-200';

$variants = [
    'primary' => 'bg-primary-100 text-primary-800',
    'secondary' => 'bg-secondary-100 text-secondary-800',
    'success' => 'bg-success-100 text-success-800',
    'warning' => 'bg-warning-100 text-warning-800',
    'danger' => 'bg-danger-100 text-danger-800',
    'info' => 'bg-info-100 text-info-800',
    'purple' => 'bg-purple-100 text-purple-800',
    'pink' => 'bg-pink-100 text-pink-800',
    'emerald' => 'bg-emerald-100 text-emerald-800',
];

$sizes = [
    'xs' => 'px-2 py-1 text-xs',
    'sm' => 'px-2.5 py-1.5 text-xs',
    'md' => 'px-3 py-1.5 text-sm',
    'lg' => 'px-4 py-2 text-base',
];

$roundedClasses = [
    'none' => 'rounded-none',
    'sm' => 'rounded-sm',
    'md' => 'rounded-md',
    'lg' => 'rounded-lg',
    'full' => 'rounded-full',
];

$variantClass = $variants[$variant] ?? $variants['primary'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
$roundedClass = $roundedClasses[$rounded] ?? $roundedClasses['full'];

$dottedClass = $dotted ? 'border border-dashed border-current opacity-80' : '';

$classes = "$baseClasses $variantClass $sizeClass $roundedClass $dottedClass gap-1";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
