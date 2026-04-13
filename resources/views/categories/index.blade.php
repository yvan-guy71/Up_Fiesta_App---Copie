<!DOCTYPE html>
<html lang="fr" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les catégories - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        html.dark body { background-color: #020617; color: #e5e7eb; }
        html.dark .bg-white { background-color: #1a1f2e; }
        html.dark .bg-slate-50 { background-color: #1e293b; }
        html.dark .border-slate-100 { border-color: #334155; }
        html.dark .text-slate-900 { color: #f1f5f9; }
        html.dark .text-slate-500 { color: #cbd5e1; }
        html.dark .text-slate-400 { color: #94a3b8; }
        html.dark .text-slate-600 { color: #cbd5e1; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <!-- Header simple -->
    <header class="bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 py-6 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <a href="/" class="text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Retour à l'accueil</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-16 px-4">
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-black dark:text-white mb-4">Découvrez nos services</h1>
            <p class="text-slate-500 dark:text-slate-400 text-lg">Trouvez le professionnel idéal pour tous vos besoins au Togo.</p>
        </div>

        <!-- Grille des catégories avec prestataires -->
        <div class="space-y-24">
            @foreach($categories as $category)
                @php
                    $categoryImages = [
                        'traiteur' => 'images/categories/perto.jpg',
                        'decoration' => 'images/categories/deco.png',
                        'photographie-video' => 'images/categories/photo.jpg',
                        'animation-dj' => 'images/categories/DJ.jpg',
                        'location-salle' => 'images/categories/location.webp',
                        'securite' => 'images/categories/secure.jpeg',
                        'maquillage-coiffure' => 'images/categories/coiffure.jpg',
                        'location-voiture' => 'images/categories/voiture.jpeg',
                        'hotesse-accueil' => 'images/categories/hotesse.jpg',
                    ];
                    $imagePath = $categoryImages[$category->slug] ?? 'images/categories/default.jpg';
                @endphp
                <div id="category-{{ $category->id }}" class="scroll-mt-24">
                    <div class="relative h-48 md:h-64 rounded-3xl overflow-hidden mb-8 shadow-lg">
                        <img src="{{ asset($imagePath) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 flex items-center">
                            <div class="px-8 md:px-12">
                                <h2 class="text-3xl md:text-4xl font-black text-white mb-2">{{ $category->name }}</h2>
                                <p class="text-indigo-100 font-medium">{{ $category->providers_count }} professionnel(s) disponible(s)</p>
                            </div>
                        </div>
                        <a href="{{ route('categories.show', $category->id) }}" class="absolute top-6 right-6 bg-white/20 backdrop-blur-md hover:bg-white/40 text-white px-5 py-2 rounded-xl text-sm font-bold transition-all border border-white/30">
                            Voir la fiche catégorie
                        </a>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse($category->providers as $provider)
                            @include('partials.provider-card', ['provider' => $provider])
                        @empty
                            <div class="col-span-full py-8 text-center bg-slate-50 dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-100 dark:border-slate-700">
                                <p class="text-slate-400 dark:text-slate-500 font-medium">Aucun professionnel disponible pour le moment dans cette catégorie.</p>
                                <a href="{{ route('service-requests.create') }}" class="inline-block mt-4 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Exprimer un besoin spécifique →
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <footer class="bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 py-12 px-4 mt-20 text-center">
        <p class="text-slate-400 dark:text-slate-500 text-sm font-medium">&copy; 2026 Up Fiesta. Tous droits réservés.</p>
    </footer>

    <script>
        // Dark mode toggle
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = localStorage.getItem('theme') === 'dark';
            if (isDark) {
                document.documentElement.classList.add('dark');
            }
        });
    </script>
</body>
</html>