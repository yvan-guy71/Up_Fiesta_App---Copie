@php
    $type = null;
    $message = null;

    if (session()->has('success')) {
        $type = 'success';
        $message = session('success');
    } elseif (session()->has('error')) {
        $type = 'error';
        $message = session('error');
    } elseif (session()->has('info')) {
        $type = 'info';
        $message = session('info');
    } elseif (session()->has('warning')) {
        $type = 'info';
        $message = session('warning');
    }
@endphp

@if($message)
    <div
        id="flash-message"
        class="fixed bottom-5 right-5 z-50 max-w-sm w-full shadow-2xl rounded-2xl p-4 flex items-center gap-3 border
        @if($type === 'success') bg-white border-green-100 text-green-800 @elseif($type === 'error') bg-white border-red-100 text-red-800 @else bg-white border-amber-100 text-amber-900 @endif"
    >
        <div
            class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
            @if($type === 'success') bg-green-100 text-green-600 @elseif($type === 'error') bg-red-100 text-red-600 @else bg-amber-100 text-amber-600 @endif"
        >
            @if($type === 'success')
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            @elseif($type === 'error')
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @endif
        </div>

        <div class="flex-1">
            <p class="text-sm font-bold">{{ $message }}</p>
        </div>

        <button
            type="button"
            onclick="document.getElementById('flash-message')?.classList.add('hidden')"
            class="text-gray-400 hover:text-gray-600 transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <script>
        setTimeout(function () {
            var el = document.getElementById('flash-message');
            if (el) {
                el.classList.add('hidden');
            }
        }, 5000);
    </script>
@endif
