{{-- Divider Component --}}
@props([
    'orientation' => 'horizontal',
    'color' => 'secondary-200',
])

@if ($orientation === 'horizontal')
    <div {{ $attributes->merge(['class' => "w-full h-px bg-$color"]) }}></div>
@else
    <div {{ $attributes->merge(['class' => "h-full w-px bg-$color"]) }}></div>
@endif
