<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $provider->name }} - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">

    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-EBCV83H4WN"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-EBCV83H4WN');
</script>
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="64x64" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="64x64" href="/favicon-16.png">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script>
            tailwind.config = { darkMode: 'class' };
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            html { scroll-behavior: smooth; }
            html.dark body { background-color: #020617; color: #e5e7eb; }
            html.dark header { background-color: rgba(15, 23, 42, 0.95); border-bottom-color: #1f2937; }
            html.dark main { background-color: #020617; }
            html.dark .bg-white { background-color: #1a1f2e; border-color: #2d3748; }
            html.dark .bg-slate-50 { background-color: #111827; }
            html.dark .bg-slate-100 { background-color: #111827; }
            html.dark .text-slate-300, html.dark .text-slate-500, html.dark .text-slate-600, html.dark .text-slate-700, html.dark .text-slate-900 { color: #e5e7eb; }
            html.dark .border-slate-100, html.dark .border-slate-200 { border-color: #2d3748; }
            html.dark .bg-indigo-50 { background-color: #312e81; }
            html.dark .text-indigo-600 { color: #a5b4fc; }
            html.dark footer { background-color: #111827; }
        </style>
    @endif
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <header class="bg-white dark:bg-slate-800 shadow-sm sticky top-0 z-50 border-b dark:border-slate-700">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <a href="{{ route('home') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">← Retour à l'accueil</a>
        </nav>
    </header>
    <main class="max-w-7xl mx-auto py-12 px-4">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Left Column: Info -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-xl dark:shadow-slate-900 border border-slate-100 dark:border-slate-700">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="w-32 h-32 bg-slate-100 dark:bg-slate-700 rounded-2xl overflow-hidden flex-shrink-0">
                            @if($provider->logo)
                                <img src="{{ asset('storage/' . $provider->logo) }}" alt="{{ $provider->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-xs font-bold rounded-full uppercase tracking-wider">{{ $provider->category->name }}</span>
                                <span class="text-slate-500 dark:text-slate-400 text-sm flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $provider->city->name }}, Togo
                                </span>
                            </div>
                            <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-4">{{ $provider->name }}</h1>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $provider->description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Media Gallery -->
                @if($provider->media->count() > 0)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-xl dark:shadow-slate-900 border border-slate-100 dark:border-slate-700">
                        <h2 class="text-2xl font-bold dark:text-white mb-6">Galerie Réalisations</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($provider->media as $item)
                                <div class="relative group aspect-square rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-100 dark:border-slate-700">
                                    @if($item->type === 'image')
                                        <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                                    @else
                                        @php
                                            $videoId = '';
                                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $item->file_path, $match)) {
                                                $videoId = $match[1];
                                            }
                                        @endphp
                                        @if($videoId)
                                            <div class="relative w-full h-full">
                                                <img src="https://img.youtube.com/vi/{{ $videoId }}/mqdefault.jpg" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition">
                                                    <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 fill-current" viewBox="0 0 20 20">
                                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.841z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    <!-- Fullscreen/Link Overlay -->
                                    <a href="{{ $item->type === 'image' ? asset('storage/' . $item->file_path) : $item->file_path }}" 
                                       target="_blank"
                                       class="absolute inset-0 z-10 opacity-0 group-hover:opacity-100 transition-opacity bg-indigo-600/20 flex items-end p-3">
                                        @if($item->title)
                                            <span class="text-[10px] font-bold text-white bg-indigo-600/80 px-2 py-1 rounded-lg backdrop-blur-sm truncate">{{ $item->title }}</span>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviews Section -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-xl dark:shadow-slate-900 border border-slate-100 dark:border-slate-700">
                    <h2 class="text-2xl font-bold dark:text-white mb-8">Avis des clients</h2>
                    @forelse($provider->reviews as $review)
                        <div class="border-b border-slate-50 dark:border-slate-700 last:border-0 py-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $review->user->name }}</span>
                                <div class="flex text-amber-400">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i < $review->rating ? 'fill-current' : 'text-slate-200 dark:text-slate-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 italic">"{{ $review->comment }}"</p>
                        </div>
                    @empty
                        <p class="text-slate-500 dark:text-slate-400">Aucun avis pour le moment.</p>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Actions -->
            <div class="space-y-6">
                <div class="bg-indigo-600 dark:bg-indigo-700 rounded-3xl p-8 text-white shadow-xl dark:shadow-2xl shadow-indigo-100 dark:shadow-indigo-900/50">
                    <div class="mb-6">
                        <span class="text-indigo-100 text-sm">Tarif de base</span>
                        <div class="text-4xl font-black">{{ number_format($provider->base_price, 0, ',', ' ') }} <span class="text-xl">XOF</span></div>
                    </div>
                    @php
                        $contactUrl = route('login');
                        if (auth()->check()) {
                            // Redirection vers la messagerie avec Up Fiesta (Admin ID 1)
                            $contactUrl = route('messages.show', ['user' => 1, 'needs_provider' => $provider->id]);
                        }
                    @endphp
                    <div class="space-y-3">
                        <a href="{{ $contactUrl }}" class="block w-full py-4 bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-300 text-center font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-600 transition shadow-lg">
                            Exprimer mes besoins
                        </a>
                        <button onclick="openBookingModal({{ $provider->id }}, '{{ $provider->name }}')" class="block w-full py-4 bg-indigo-500 dark:bg-indigo-600 text-white text-center font-bold rounded-2xl hover:bg-indigo-400 dark:hover:bg-indigo-700 transition border border-indigo-400 dark:border-indigo-600">
                            Réserver maintenant
                        </button>
                    </div>
                    <p class="text-indigo-100 dark:text-indigo-300 text-xs text-center mt-6">Paiement sécurisé via T-Money ou Flooz disponible après confirmation.</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-xl dark:shadow-slate-900 border border-slate-100 dark:border-slate-700">
                    <h3 class="font-bold dark:text-white mb-4">Informations</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-slate-600 dark:text-slate-400 text-sm">
                        </li>
                        <li class="flex items-center gap-3 text-slate-600 dark:text-slate-400 text-sm">
                            <div class="w-8 h-8 bg-slate-50 dark:bg-slate-700 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span>Réponse rapide (moins de 2h)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 z-[100] hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-md w-full p-8 shadow-2xl transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Réserver un service <span id="modalProviderName" class="text-indigo-600"></span></h2>
                <button onclick="closeBookingModal()" class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="bookingForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Date souhaitée</label>
                        <input type="date" name="event_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-slate-50 dark:bg-slate-700 dark:text-white border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none dark:focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Détails du besoin</label>
                        <textarea name="event_details" rows="3" placeholder="Lieu, description de la tâche, nombre de personnes..." class="w-full bg-slate-50 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex gap-4">
                    <button type="button" onclick="closeBookingModal()" class="flex-1 px-6 py-3 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Annuler</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 dark:shadow-indigo-900/50">Confirmer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openBookingModal(id, name) {
            @guest
                window.location.href = "{{ route('login') }}";
                return;
            @endguest
            
            document.getElementById('modalProviderName').innerText = name;
            document.getElementById('bookingForm').action = "/reserver/" + id;
            document.getElementById('bookingModal').classList.remove('hidden');
            document.getElementById('bookingModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.getElementById('bookingModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            let modal = document.getElementById('bookingModal');
            if (event.target == modal) {
                closeBookingModal();
            }
        }
    </script>

    @include('partials.footer')
</body>
</html>
