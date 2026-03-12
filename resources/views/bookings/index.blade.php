<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - Up Fiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold mb-8">Mes Réservations</h1>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Professionnel</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Date prévue</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Prix total</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Statut</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $booking->provider->name }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->provider->category->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $booking->event_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ number_format($booking->total_price, 0, ',', ' ') }} XOF
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if($booking->status == 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($booking->status == 'confirmed') bg-green-100 text-green-700
                                    @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                                @if($booking->payment_status == 'paid')
                                    <span class="ml-2 px-3 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">PAYÉ</span>
                                @elseif($booking->payment_status == 'pending')
                                    <span class="ml-2 px-3 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700">EN ATTENTE</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-3">
                                    <div class="flex gap-4">
                                        @if($booking->payment_status == 'unpaid' && $booking->status != 'cancelled')
                                            <div class="flex flex-col gap-1">
                                                <a href="{{ route('payment.checkout', ['booking' => $booking->id, 'method' => 'tmoney']) }}" class="text-[10px] bg-yellow-400 text-yellow-900 px-2 py-1 rounded font-bold hover:bg-yellow-500 transition text-center">T-Money</a>
                                                <a href="{{ route('payment.checkout', ['booking' => $booking->id, 'method' => 'flooz']) }}" class="text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold hover:bg-blue-700 transition text-center">Flooz</a>
                                                <a href="{{ route('payment.checkout', ['booking' => $booking->id, 'method' => 'card']) }}" class="text-[10px] bg-indigo-600 text-white px-2 py-1 rounded font-bold hover:bg-indigo-700 transition text-center">Carte</a>
                                                <p class="text-[10px] text-slate-500 mt-1">Après redirection, suivez les instructions PayGate. Pour T-Money/Flooz, gardez votre téléphone à portée pour valider l'opération.</p>
                                            </div>
                                        @endif
                                        @if($booking->payment_status == 'pending')
                                            <p class="text-[10px] text-yellow-700 bg-yellow-50 border border-yellow-100 px-2 py-1 rounded font-bold">Votre paiement est en cours de validation (jusqu'à 15 min).</p>
                                        @endif
                                        <a href="{{ route('service-requests.create', ['provider_id' => $booking->provider->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium self-center">Exprimer un nouveau besoin</a>
                                    </div>

                                    @if($booking->status === 'completed')
                                        @if($booking->review)
                                            <p class="text-xs text-green-700 bg-green-50 border border-green-100 px-3 py-1 rounded-full inline-block">Avis déjà envoyé. Merci !</p>
                                        @else
                                            <form action="{{ route('reviews.store', $booking->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
                                                @csrf
                                                <div class="flex items-center gap-1">
                                                    <label for="rating-{{ $booking->id }}" class="text-xs font-semibold text-gray-600">Note</label>
                                                    <select id="rating-{{ $booking->id }}" name="rating" class="text-xs border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                        @for($i = 5; $i >= 1; $i--)
                                                            <option value="{{ $i }}">{{ $i }} ★</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <input type="text" name="comment" placeholder="Votre avis (optionnel)" class="flex-1 text-xs border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                                                <button type="submit" class="text-xs bg-indigo-600 text-white px-3 py-1 rounded font-bold hover:bg-indigo-700 transition">Envoyer</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Vous n'avez aucune réservation pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </div>
</body>
</html>
