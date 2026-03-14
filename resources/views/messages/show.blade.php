<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation avec {{ $contact->name }} - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex flex-col">
    <x-flash-messages />
    <header class="bg-white shadow-sm p-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold">
                {{ substr($contact->name, 0, 1) }}
            </div>
            <h1 class="font-bold text-lg">{{ $contact->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('messages.conversation.destroy', $contact->id) }}" onsubmit="return confirm('Supprimer toute la discussion avec {{ $contact->name }} ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-600 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Supprimer la discussion
                </button>
            </form>
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Accueil
            </a>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-4 space-y-4">
        @foreach($messages as $message)
            @php
                $sender = $message->sender;
                $isMe = $message->sender_id == auth()->id();
                $senderName = $sender->name;
                $senderRole = '';
                if ($sender->role === 'admin') $senderRole = 'Administration';
                elseif ($sender->role === 'provider') $senderRole = 'Prestataire';
                elseif ($sender->role === 'client') $senderRole = 'Client';
            @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] sm:max-w-[70%]">
                    <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ $senderName }}</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-medium">{{ $senderRole }}</span>
                    </div>
                    <div class="relative group p-4 rounded-2xl {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-900 rounded-bl-none shadow-sm' }}">
                        @if($message->provider_id && $message->provider)
                            <div class="mb-3 p-3 bg-white/10 rounded-xl border border-white/20">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 bg-white rounded-lg overflow-hidden flex-shrink-0">
                                        @if($message->provider->logo)
                                            <img src="{{ asset('storage/' . $message->provider->logo) }}" alt="{{ $message->provider->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-600 font-bold">
                                                {{ substr($message->provider->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-sm truncate {{ $isMe ? 'text-white' : 'text-slate-900' }}">{{ $message->provider->name }}</h4>
                                        <p class="text-[10px] {{ $isMe ? 'text-indigo-100' : 'text-slate-500' }}">{{ $message->provider->category->name }} • {{ $message->provider->city->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('providers.show', $message->provider->id) }}" class="block w-full py-2 text-center text-xs font-bold rounded-lg transition-all {{ $isMe ? 'bg-white text-indigo-600 hover:bg-indigo-50' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                    Voir le profil
                                </a>
                            </div>
                        @endif
                        <p class="text-sm">{{ $message->content }}</p>
                        <div class="flex items-center justify-end gap-1 mt-1 opacity-70">
                            <span class="text-[10px]">{{ $message->created_at->format('H:i') }}</span>
                            @if($isMe)
                                @if($message->is_read)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-sky-300" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        <path d="M10.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l1.293-1.293a1 1 0 011.414 0z" transform="translate(4, 0)" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white/60" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        <path d="M10.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l1.293-1.293a1 1 0 011.414 0z" transform="translate(4, 0)" />
                                    </svg>
                                @endif
                            @endif
                        </div>
                        @if($isMe)
                            <form method="POST" action="{{ route('messages.destroy', $message->id) }}" class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200" onsubmit="return confirm('Supprimer ce message ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-[10px] hover:bg-red-600 shadow-sm">
                                    ×
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </main>

    <footer class="bg-white p-4 border-t border-gray-100">
        @if(auth()->user()->role === 'admin' && $contact->role === 'client')
            <div class="mb-4">
                <button type="button" onclick="toggleProviderList()" class="flex items-center gap-2 text-xs font-bold text-indigo-600 bg-indigo-50 px-4 py-2 rounded-xl hover:bg-indigo-100 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Proposer un prestataire
                </button>
                
                <div id="provider-list" class="hidden mt-3 max-h-48 overflow-y-auto border border-slate-100 rounded-2xl bg-slate-50 p-2 space-y-1">
                    @foreach($providers as $p)
                        <button type="button" onclick="proposeProvider({{ $p->id }}, '{{ addslashes($p->name) }}')" class="w-full flex items-center gap-3 p-2 hover:bg-white hover:shadow-sm rounded-xl transition-all text-left">
                            <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center text-xs font-bold">
                                {{ substr($p->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-800 truncate">{{ $p->name }}</div>
                                <div class="text-[10px] text-slate-500">{{ $p->category->name }} • {{ $p->city->name }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if(auth()->user()->role === 'client' && $contact->role === 'admin')
            <div class="mb-4 flex flex-wrap gap-2">
                @php
                    $suggestions = [
                        "Je cherche un traiteur pour un mariage.",
                        "Quels sont les photographes disponibles ?",
                        "J'ai besoin d'aide pour organiser mon anniversaire.",
                        "Comment réserver un lieu de réception ?"
                    ];
                @endphp
                @foreach($suggestions as $suggestion)
                    <button type="button" onclick="setMessage('{{ addslashes($suggestion) }}')" class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full hover:bg-indigo-100 transition-colors border border-indigo-100">
                        {{ $suggestion }}
                    </button>
                @endforeach
            </div>
        @endif
        <form id="message-form" action="{{ route('messages.store', $contact->id) }}" method="POST" class="flex gap-4">
            @csrf
            <input type="hidden" id="provider-id-input" name="provider_id" value="{{ request('needs_provider') }}">
            <textarea name="content" id="message-input" rows="1" class="flex-1 bg-gray-100 border-none rounded-2xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none resize-none transition-all" placeholder="Tapez votre message ici...">{{ $prefillMessage ?? '' }}</textarea>
            <button type="submit" class="bg-indigo-600 text-white p-3 rounded-full hover:bg-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
    </footer>

    <script>
        function toggleProviderList() {
            const list = document.getElementById('provider-list');
            list.classList.toggle('hidden');
        }

        function proposeProvider(id, name) {
            if(confirm("Voulez-vous proposer " + name + " ?")) {
                document.getElementById('provider-id-input').value = id;
                document.getElementById('message-input').required = false;
                document.getElementById('message-form').submit();
            }
        }

        function setMessage(text) {
            document.getElementById('message-input').value = text;
            document.getElementById('message-input').focus();
        }

        // Scroll to bottom
        const main = document.querySelector('main');
        main.scrollTop = main.scrollHeight;
    </script>
</body>
</html>
