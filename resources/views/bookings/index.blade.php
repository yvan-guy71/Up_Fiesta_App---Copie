<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes Réservations - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script>
            tailwind.config = { darkMode: 'class' };
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            html { scroll-behavior: smooth; }
            html.dark body { background-color: #020617; color: #f1f5f9; }
            html.dark header { background-color: rgba(15, 23, 42, 0.95); border-bottom-color: #1f2937; }
            html.dark main { background-color: #020617; }
            html.dark .bg-white { background-color: #1e293b; border-color: #334155; }
            html.dark .bg-slate-50 { background-color: #0f172a; }
            html.dark .bg-slate-100 { background-color: #1e293b; }
            html.dark .text-slate-300, html.dark .text-slate-500, html.dark .text-slate-600, html.dark .text-slate-700, html.dark .text-slate-900 { color: #cbd5e1; }
            html.dark .border-slate-100, html.dark .border-slate-200 { border-color: #334155; }
            html.dark .bg-indigo-50 { background-color: #312e81; }
            html.dark .text-indigo-600 { color: #a5b4fc; }
            html.dark footer { background-color: #0f172a; }
            html.dark .shadow-sm { shadow: none; }
            * { transition-property: background-color, border-color, color; transition-duration: 200ms; }
        </style>
    @endif
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100">
    <header class="bg-white dark:bg-slate-800 shadow-sm sticky top-0 z-50 border-b dark:border-slate-700">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <a href="{{ route('home') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">← Retour à l'accueil</a>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">Mes Réservations</h1>
            <p class="text-slate-600 dark:text-slate-400">Gérez et suivez toutes vos réservations de services</p>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block bg-white dark:bg-slate-800 rounded-2xl shadow-sm dark:shadow-xl dark:shadow-slate-900/50 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Professionnel</th>
                        <th class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Date prévue</th>
                        <th class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Prix total</th>
                        <th class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Statut</th>
                        <th class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($allReservations as $item)
                        @php
                            $isBooking = $item instanceof \App\Models\Booking;
                            $isAssignedService = $item instanceof \App\Models\AssignedService;
                            
                            $provider = $isBooking ? $item->provider : $item->provider;
                            $eventDate = $isBooking ? $item->event_date : $item->serviceRequest->event_date;
                            $price = $isBooking ? $item->total_price : ($item->serviceRequest->budget ?? $item->provider->base_price ?? 0);
                            $status = $isBooking ? $item->status : $item->status;
                            $paymentStatus = $isBooking ? $item->payment_status : null;
                            $itemId = $item->id;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $provider->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $provider->category->name ?? 'Services' }}</div>
                            </td>
                            <td class="px-6 py-5 text-slate-600 dark:text-slate-400">
                                {{ $eventDate->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-5 font-bold text-slate-900 dark:text-white">
                                {{ number_format($price, 0, ',', ' ') }} XOF
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest w-fit
                                        @if($status == 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                        @elseif($status == 'confirmed' || $status == 'accepted') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                        @elseif($status == 'cancelled' || $status == 'rejected') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                        @elseif($status == 'completed') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                        @else bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 @endif">
                                        @if($isAssignedService && $status == 'accepted')
                                            Acceptée - À réserver
                                        @else
                                            {{ ucfirst($status) }}
                                        @endif
                                    </span>
                                    @if($isBooking && $paymentStatus == 'paid')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 uppercase tracking-widest w-fit">PAYÉ</span>
                                    @elseif($isBooking && $paymentStatus == 'pending')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 uppercase tracking-widest w-fit">EN ATTENTE</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-2">
                                    @if($isBooking)
                                        @if($item->payment_status == 'unpaid' && $item->status != 'cancelled')
                                            <a href="{{ route('payment.checkout', ['booking' => $item->id, 'method' => 'tmoney']) }}" class="text-xs bg-yellow-400 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100 px-3 py-1.5 rounded-lg font-bold hover:shadow-md transition-all">T-Money</a>
                                            <a href="{{ route('payment.checkout', ['booking' => $item->id, 'method' => 'flooz']) }}" class="text-xs bg-blue-600 dark:bg-blue-700 text-white px-3 py-1.5 rounded-lg font-bold hover:shadow-md transition-all">Flooz</a>
                                            <a href="{{ route('payment.checkout', ['booking' => $item->id, 'method' => 'card']) }}" class="text-xs bg-indigo-600 dark:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-bold hover:shadow-md transition-all">Carte</a>
                                        @endif
                                        @if($item->payment_status == 'pending')
                                            <span class="text-xs text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 px-3 py-1.5 rounded-lg font-bold">Validation en cours</span>
                                        @endif
                                        <a href="{{ route('service-requests.create', ['provider_id' => $item->provider->id]) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-bold px-3 py-1.5 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-all">Nouveau besoin</a>
                                    @elseif($isAssignedService)
                                        <a href="{{ route('bookings.show', $item->id) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-bold px-3 py-1.5 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-all">Voir détails</a>
                                        <form action="{{ route('bookings.createFromAssignedService', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-xs bg-green-600 dark:bg-green-700 text-white px-3 py-1.5 rounded-lg font-bold hover:shadow-md transition-all">Réserver</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="text-slate-400 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-slate-500 dark:text-slate-400">Vous n'avez aucune réservation pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @forelse($allReservations as $item)
                @php
                    $isBooking = $item instanceof \App\Models\Booking;
                    $isAssignedService = $item instanceof \App\Models\AssignedService;
                    
                    $provider = $isBooking ? $item->provider : $item->provider;
                    $eventDate = $isBooking ? $item->event_date : $item->serviceRequest->event_date;
                    $price = $isBooking ? $item->total_price : ($item->serviceRequest->budget ?? $item->provider->base_price ?? 0);
                    $status = $isBooking ? $item->status : $item->status;
                    $paymentStatus = $isBooking ? $item->payment_status : null;
                    $review = $isBooking ? $item->review : null;
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm dark:shadow-xl dark:shadow-slate-900/50 border border-slate-100 dark:border-slate-700 overflow-hidden p-5 space-y-4">
                    <!-- Provider Info -->
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-lg">{{ $provider->name }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $provider->category->name ?? 'Services' }}</p>
                    </div>

                    <!-- Date and Price -->
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mb-1">Date prévue</p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $eventDate->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mb-1">Prix total</p>
                            <p class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ number_format($price, 0, ',', ' ') }} XOF</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex flex-wrap gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-widest
                            @if($status == 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                            @elseif($status == 'confirmed' || $status == 'accepted') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                            @elseif($status == 'cancelled' || $status == 'rejected') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                            @elseif($status == 'completed') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                            @else bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 @endif">
                            @if($isAssignedService && $status == 'accepted')
                                Acceptée - À réserver
                            @else
                                {{ ucfirst($status) }}
                            @endif
                        </span>
                        @if($isBooking && $paymentStatus == 'paid')
                            <span class="px-3 py-1.5 rounded-full text-xs font-black bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 uppercase tracking-widest">PAYÉ</span>
                        @elseif($isBooking && $paymentStatus == 'pending')
                            <span class="px-3 py-1.5 rounded-full text-xs font-black bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 uppercase tracking-widest">EN ATTENTE</span>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        @if($isBooking)
                            @if($item->payment_status == 'unpaid' && $item->status != 'cancelled')
                                <div class="space-y-2">
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest">Payer maintenant</p>
                                    <div class="grid grid-cols-3 gap-2">
                                        <a href="{{ route('payment.checkout', ['booking' => $item->id, 'method' => 'tmoney']) }}" class="text-xs bg-yellow-400 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100 px-3 py-2 rounded-lg font-bold hover:shadow-md transition-all text-center">T-Money</a>
                                        <a href="{{ route('payment.checkout', ['booking' => $item->id, 'method' => 'flooz']) }}" class="text-xs bg-blue-600 dark:bg-blue-700 text-white px-3 py-2 rounded-lg font-bold hover:shadow-md transition-all text-center">Flooz</a>
                                        <a href="{{ route('payment.checkout', ['booking' => $item->id, 'method' => 'card']) }}" class="text-xs bg-indigo-600 dark:bg-indigo-700 text-white px-3 py-2 rounded-lg font-bold hover:shadow-md transition-all text-center">Carte</a>
                                    </div>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 italic">Après redirection, suivez les instructions PayGate.</p>
                                </div>
                            @endif
                            @if($item->payment_status == 'pending')
                                <p class="text-xs text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 px-3 py-2 rounded-lg font-bold">Votre paiement est en cours de validation (jusqu'à 15 min).</p>
                            @endif

                            <a href="{{ route('service-requests.create', ['provider_id' => $item->provider->id]) }}" class="block w-full text-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-bold px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-all">Exprimer un nouveau besoin</a>

                            @if($item->status === 'completed')
                                @if($review)
                                    <p class="text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-3 py-2 rounded-lg font-bold text-center">Avis déjà envoyé. Merci !</p>
                                @else
                                    <form action="{{ route('reviews.store', $item->id) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <label for="rating-{{ $item->id }}" class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest block">Laisser un avis</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <select id="rating-{{ $item->id }}" name="rating" class="text-xs border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                @for($i = 5; $i >= 1; $i--)
                                                    <option value="{{ $i }}">{{ $i }} ★</option>
                                                @endfor
                                            </select>
                                            <button type="submit" class="text-xs bg-indigo-600 dark:bg-indigo-700 text-white px-3 py-2 rounded-lg font-bold hover:shadow-md transition-all">Envoyer</button>
                                        </div>
                                        <input type="text" name="comment" placeholder="Votre avis (optionnel)" class="w-full text-xs border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-slate-400 dark:placeholder-slate-500" />
                                    </form>
                                @endif
                            @endif
                        @elseif($isAssignedService)
                            <a href="{{ route('bookings.show', $item->id) }}" class="block w-full text-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-bold px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-all">Voir détails</a>
                            <form action="{{ route('bookings.createFromAssignedService', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-center text-white bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 font-bold px-4 py-2 rounded-lg transition-all">Réserver maintenant</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm dark:shadow-xl dark:shadow-slate-900/50 border border-slate-100 dark:border-slate-700 px-6 py-12 text-center">
                    <div class="text-slate-400 dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-slate-500 dark:text-slate-400">Vous n'avez aucune réservation pour le moment.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $allReservations->links() }}
        </div>
    </main>
</body>
</html>
