@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('bookings.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour à mes réservations
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Réservation #{{ $booking->id }}</h1>
            <p class="text-gray-500 mt-2">Créée le {{ $booking->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations de la réservation</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Date prévue</label>
                        <p class="text-gray-800">{{ $booking->event_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Détails</label>
                        <p class="text-gray-700">{{ $booking->event_details ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Statut</label>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                            @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Prix total</label>
                        <p class="text-gray-800 text-lg font-bold">{{ number_format($booking->total_price, 0, ',', ' ') }} CFA</p>
                    </div>
                </div>

                @if($booking->status === 'confirmed' && $booking->payment_status !== 'paid')
                    <div class="mt-8 p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <h3 class="text-lg font-bold text-indigo-900 mb-4">Procéder au paiement</h3>
                        <p class="text-sm text-indigo-700 mb-6">Votre réservation a été confirmée ! Vous pouvez maintenant choisir votre mode de paiement préféré pour finaliser la commande.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('payment.checkout', ['booking' => $booking->id, 'method' => 'tmoney']) }}" 
                               class="flex items-center justify-center gap-3 px-6 py-4 bg-white border-2 border-indigo-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all group">
                                <img src="{{ asset('images/payments/tmoney.png') }}" alt="TMoney" class="h-8 w-auto">
                                <span class="font-bold text-slate-700">TMoney</span>
                            </a>
                            <a href="{{ route('payment.checkout', ['booking' => $booking->id, 'method' => 'flooz']) }}" 
                               class="flex items-center justify-center gap-3 px-6 py-4 bg-white border-2 border-indigo-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all group">
                                <img src="{{ asset('images/payments/flooz.png') }}" alt="Moov Money" class="h-8 w-auto">
                                <span class="font-bold text-slate-700">Moov Money</span>
                            </a>
                        </div>
                        <p class="text-[10px] text-center text-indigo-400 mt-4 uppercase tracking-widest font-bold">Sécurisé par PayGate Togo</p>
                    </div>
                @endif
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Prestataire</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $booking->provider->name }}</h3>
                    @if($booking->provider->email)
                        <p class="text-gray-600">✉️ {{ $booking->provider->email }}</p>
                    @endif
                    @if($booking->provider->city)
                        <p class="text-gray-600">📍 {{ $booking->provider->city->name }}</p>
                    @endif
                </div>

                @if($booking->review)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Votre avis</h3>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $i <= $booking->review->rating ? 'text-yellow-400' : 'text-gray-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-gray-700">{{ $booking->review->comment }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
