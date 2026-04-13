{{-- Input Component Professionnel --}}
@props([
    'label' => null,
    'type' => 'text',
    'error' => null,
    'hint' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'required' => false,
    'disabled' => false,
    'size' => 'md',
])

@php
$sizeClasses = [
    'sm' => 'input-sm',
    'md' => 'input-md', 
    'lg' => 'input-lg',
];

$baseInputClasses = 'w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-blue-500/20 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent disabled:bg-slate-100 dark:disabled:bg-slate-700 disabled:cursor-not-allowed bg-white dark:bg-slate-800 shadow-sm hover:border-slate-400 dark:hover:border-blue-500/30 focus:shadow-md dark:focus:shadow-blue-900/30';

$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="w-full">
    @if ($label)
        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-danger-600 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative group">
        @if ($icon && $iconPosition === 'left')
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                {!! $icon !!}
            </div>
        @endif

        <input
            type="{{ $type }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => $baseInputClasses . ($icon && $iconPosition === 'left' ? ' pl-10' : '') . ($icon && $iconPosition === 'right' ? ' pr-10' : '') . ($error ? ' border-danger-500 focus:ring-danger-500' : ''),
            ]) }}
        />

        @if ($icon && $iconPosition === 'right')
            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                {!! $icon !!}
            </div>
        @endif
    </div>

    @if ($error)
        <p class="mt-2 text-sm text-danger-600 font-medium">{{ $error }}</p>
    @elseif ($hint)
        <p class="mt-2 text-sm text-secondary-500">{{ $hint }}</p>
    @endif
</div>
