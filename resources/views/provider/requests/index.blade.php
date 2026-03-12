<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes demandes - Espace prestataire</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <a href="{{ route('home') }}" class="text-sm font-bold text-indigo-600 hover:underline">← Retour à l'accueil</a>
            @include('partials.notifications')
        </nav>
    </header>

    <main class="max-w-4xl mx-auto py-12 px-4">
        <x-flash-messages />
        <h1 class="text-3xl font-black text-slate-900 mb-6">Demandes qui vous concernent</h1>

        @if($requests->isEmpty())
            <p class="text-slate-600">Aucune demande pour le moment.</p>
        @else
            <div class="space-y-4">
                @foreach($requests as $req)
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="font-bold text-lg">{{ $req->subject }}</h2>
                                <p class="text-sm text-slate-500">Envoyée le {{ $req->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $req->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($req->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </div>
                        <p class="mt-4 whitespace-pre-line">{{ $req->description }}</p>

                        @if($req->status === 'pending')
                            <form action="{{ route('service-requests.status', $req) }}" method="POST" class="mt-4 flex gap-2">
                                @csrf
                                <button name="status" value="approved" type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Accepter</button>
                                <button name="status" value="rejected" type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Refuser</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>
