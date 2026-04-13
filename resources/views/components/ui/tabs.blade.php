{{-- Tabs Component Professionnel --}}
@props([
    'tabs' => [],
])

<div x-data="{ active: 0 }" class="w-full">
    <!-- Tab Buttons -->
    <div class="flex border-b border-secondary-200 gap-2 overflow-x-auto">
        @foreach ($tabs as $index => $tab)
            <button
                @click="active = {{ $index }}"
                :class="active === {{ $index }} ? 'border-b-2 border-primary-600 text-primary-600' : 'text-secondary-600 hover:text-secondary-900'"
                class="px-4 py-3 font-medium transition-all duration-200 whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-t-lg">
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>

    <!-- Tab Content -->
    <div class="mt-6">
        @foreach ($tabs as $index => $tab)
            <div x-show="active === {{ $index }}"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">
                {{ $tab['content'] ?? '' }}
            </div>
        @endforeach
    </div>
</div>
