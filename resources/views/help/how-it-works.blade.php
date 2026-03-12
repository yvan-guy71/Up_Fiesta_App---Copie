<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment ça marche - Up Fiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-8 w-auto">
                </a>
                <a href="{{ route('home') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-500">Retour à l'accueil</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xl shadow-slate-200 border border-slate-100">
            <h1 class="text-4xl font-black text-slate-900 mb-4">Comment ça marche</h1>
            <p class="text-slate-600 mb-8">Découvrez comment trouver rapidement le bon prestataire et réserver en toute sérénité.</p>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6 border border-slate-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-4">
                        1
                    </div>
                    <h2 class="text-xl font-bold mb-2">Recherchez un prestataire</h2>
                    <p class="text-slate-600">Filtrez par catégorie, localisation et besoins pour trouver le professionnel adapté.</p>
                </div>
                <div class="p-6 border border-slate-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-4">
                        2
                    </div>
                    <h2 class="text-xl font-bold mb-2">Comparez et contactez</h2>
                    <p class="text-slate-600">Consultez les profils, avis et réalisations, puis envoyez votre demande.</p>
                </div>
                <div class="p-6 border border-slate-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-4">
                        3
                    </div>
                    <h2 class="text-xl font-bold mb-2">Réservez en confiance</h2>
                    <p class="text-slate-600">Validez la prestation et suivez vos réservations depuis votre espace.</p>
                </div>
            </div>

            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('service-requests.create') }}" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl">
                    Démarrer une demande
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.293 15.95l5.657-5.657a1 1 0 000-1.414L10.293 3.222a1 1 0 011.414-1.414l6.364 6.364a3 3 0 010 4.243l-6.364 6.364a1 1 0 01-1.414-1.414z" />
                    </svg>
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold text-indigo-600 bg-white hover:bg-slate-50 rounded-xl border border-indigo-100">
                    Explorer les prestataires
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} Up Fiesta. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
