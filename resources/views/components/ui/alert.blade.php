{{-- Alert Component Professionnel --}}
@props([
    'type' => 'info',
    'dismissible' => true,
    'title' => null,
    'icon' => true,
])

@php
$baseClasses = 'rounded-lg p-4 transition-all duration-300 animate-fade-in';

$alertTypes = [
    'success' => [
        'bg' => 'bg-success-50 border border-success-200',
        'text' => 'text-success-800',
        'title' => 'text-success-900 font-semibold',
        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
    ],
    'warning' => [
        'bg' => 'bg-warning-50 border border-warning-200',
        'text' => 'text-warning-800',
        'title' => 'text-warning-900 font-semibold',
        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
    ],
    'danger' => [
        'bg' => 'bg-danger-50 border border-danger-200',
        'text' => 'text-danger-800',
        'title' => 'text-danger-900 font-semibold',
        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>',
    ],
    'info' => [
        'bg' => 'bg-info-50 border border-info-200',
        'text' => 'text-info-800',
        'title' => 'text-info-900 font-semibold',
        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
    ],
];

$typeConfig = $alertTypes[$type] ?? $alertTypes['info'];
$classes = "$baseClasses {$typeConfig['bg']} {$typeConfig['text']}";
@endphp

<div x-data="{ show: true }" x-show="show" 
    class="{{ $classes }}"
    role="alert"
    {{ $attributes }}>
    
    <div class="flex gap-3">
        @if ($icon)
            <div class="flex-shrink-0 text-xl">
                {!! $typeConfig['icon'] !!}
            </div>
        @endif

        <div class="flex-1">
            @if ($title)
                <h3 class="{{ $typeConfig['title'] }}">{{ $title }}</h3>
            @endif
            <div class="text-sm mt-{{ $title ? '1' : '0' }}">
                {{ $slot }}
            </div>
        </div>

        @if ($dismissible)
            <button type="button"
                @click="show = false"
                class="flex-shrink-0 text-current opacity-70 hover:opacity-100 transition-opacity"
                aria-label="@lang('Close')">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>
</div>
