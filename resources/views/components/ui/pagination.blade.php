{{-- Pagination Component --}}
@props([
    'items' => null,
    'paginator' => null,
])

@php
$paginator = $paginator ?? $items;
@endphp

@if ($paginator && $paginator->lastPage() > 1)
<nav class="flex items-center justify-between border-t border-secondary-200 px-4 py-3 sm:px-6">
    <!-- Info -->
    <div class="flex flex-1 justify-between sm:hidden">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center rounded-md border border-secondary-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-secondary-500 cursor-not-allowed">
                Précédent
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center rounded-md border border-secondary-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-secondary-700 hover:bg-secondary-50 transition-colors">
                Précédent
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="ml-3 inline-flex items-center rounded-md border border-secondary-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-secondary-700 hover:bg-secondary-50 transition-colors">
                Suivant
            </a>
        @else
            <span class="ml-3 inline-flex items-center rounded-md border border-secondary-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-secondary-500 cursor-not-allowed">
                Suivant
            </span>
        @endif
    </div>

    <!-- Desktop -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-secondary-700">
                Affichage <span class="font-medium">{{ $paginator->firstItem() }}</span>
                à <span class="font-medium">{{ $paginator->lastItem() }}</span>
                sur <span class="font-medium">{{ $paginator->total() }}</span>
                résultats
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-lg shadow-sm" aria-label="Pagination">
                <!-- Previous -->
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center rounded-l-lg px-2 py-2 text-secondary-400 cursor-not-allowed">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-l-lg px-2 py-2 text-secondary-400 ring-1 ring-inset ring-secondary-300 hover:bg-secondary-50 focus:z-20 focus:outline-offset-0 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                <!-- Page Numbers -->
                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="relative z-10 inline-flex items-center bg-primary-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-secondary-900 ring-1 ring-inset ring-secondary-300 hover:bg-secondary-50 focus:z-20 focus:outline-offset-0 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                <!-- Next -->
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center rounded-r-lg px-2 py-2 text-secondary-400 ring-1 ring-inset ring-secondary-300 hover:bg-secondary-50 focus:z-20 focus:outline-offset-0 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="relative inline-flex items-center rounded-r-lg px-2 py-2 text-secondary-400 cursor-not-allowed">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
</nav>
@endif
