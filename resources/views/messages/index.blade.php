<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes Messages - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = { darkMode: 'class' };
        </script>
    @endif
    <style>
        html.dark body { background-color: #020617; color: #e5e7eb; }
        html.dark .bg-white { background-color: #1a1f2e; }
        html.dark .bg-slate-50 { background-color: #1e293b; }
        html.dark .border-slate-100 { border-color: #334155; }
        html.dark .text-slate-900 { color: #f1f5f9; }
        html.dark .text-slate-600 { color: #cbd5e1; }
        html.dark .text-slate-700 { color: #cbd5e1; }
        html.dark .text-slate-500 { color: #94a3b8; }
        html.dark input, html.dark select, html.dark textarea {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        html.dark input::placeholder, html.dark textarea::placeholder { color: #64748b; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100">
    <x-flash-messages />
    <header class="bg-white dark:bg-slate-800 shadow-sm sticky top-0 z-50 border-b dark:border-slate-700">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <a href="{{ route('home') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">← Retour à l'accueil</a>
            @include('partials.notifications')
        </nav>
    </header>

    <main class="max-w-4xl mx-auto py-12 px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Ma Messagerie</h1>
            <div class="text-sm text-slate-600 dark:text-slate-400"></div>
        </div>
        
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm overflow-hidden border border-slate-100 dark:border-slate-700">
            @forelse($messages as $userId => $conversation)
                @php $lastMessage = $conversation->first(); $contact = $lastMessage->sender_id == auth()->id() ? $lastMessage->receiver : $lastMessage->sender; @endphp
                <div class="flex items-stretch border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <a href="{{ route('messages.show', $contact->id) }}" class="flex-1 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-bold">
                                    {{ substr($contact->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 dark:text-white">{{ $contact->name }}</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 truncate max-w-xs">{{ $lastMessage->content }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $lastMessage->created_at->diffForHumans() }}</span>
                                @if(!$lastMessage->is_read && $lastMessage->receiver_id == auth()->id())
                                    <div class="mt-1 w-2 h-2 bg-indigo-600 rounded-full ml-auto"></div>
                                @endif
                            </div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('messages.conversation.destroy', $contact->id) }}" class="flex items-center pr-4" onsubmit="return confirm('Supprimer toute la discussion avec {{ $contact->name }} ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 transition-colors ml-2" title="Supprimer la discussion">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-gray-500">Vous n'avez pas encore de messages.</p>
                    <a href="{{ route('home') }}" class="mt-4 inline-block text-indigo-600 font-semibold">Trouver un prestataire</a>
                </div>
            @endforelse
        </div>
    </main>
</body>
</html>
