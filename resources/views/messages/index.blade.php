<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Messages - Up Fiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">Ma Messagerie</h1>
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Retour à l'accueil
            </a>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @forelse($messages as $userId => $conversation)
                @php $lastMessage = $conversation->first(); $contact = $lastMessage->sender_id == auth()->id() ? $lastMessage->receiver : $lastMessage->sender; @endphp
                <div class="flex items-stretch border-b border-gray-100 hover:bg-gray-50 transition">
                    <a href="{{ route('messages.show', $contact->id) }}" class="flex-1 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold">
                                    {{ substr($contact->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $contact->name }}</h3>
                                    <p class="text-sm text-gray-500 truncate max-w-xs">{{ $lastMessage->content }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-400">{{ $lastMessage->created_at->diffForHumans() }}</span>
                                @if(!$lastMessage->is_read && $lastMessage->receiver_id == auth()->id())
                                    <div class="mt-1 w-2 h-2 bg-indigo-600 rounded-full ml-auto"></div>
                                @endif
                            </div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('messages.conversation.destroy', $contact->id) }}" class="flex items-center pr-4" onsubmit="return confirm('Supprimer toute la discussion avec {{ $contact->name }} ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors ml-2" title="Supprimer la discussion">
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
    </div>
</body>
</html>
