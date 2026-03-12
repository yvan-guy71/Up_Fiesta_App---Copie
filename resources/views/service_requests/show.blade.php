@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('service-requests.create') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour aux demandes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ $serviceRequest->subject }}</h1>
            <p class="text-gray-500 mt-2">Demande #{{ $serviceRequest->id }} - Créée le {{ $serviceRequest->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Détails de la demande</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Type</label>
                        <p class="text-gray-800 capitalize">
                            @if($serviceRequest->type === 'event')
                                📅 Événement
                            @else
                                🔧 Service
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Catégorie</label>
                        <p class="text-gray-800 capitalize">
                            @if($serviceRequest->kind === 'prestations')
                                Prestations
                            @elseif($serviceRequest->kind === 'domestiques')
                                Services domestiques
                            @else
                                {{ $serviceRequest->kind }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Statut</label>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                            @if($serviceRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($serviceRequest->status === 'approved') bg-green-100 text-green-800
                            @elseif($serviceRequest->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($serviceRequest->status === 'pending')
                                En attente
                            @elseif($serviceRequest->status === 'approved')
                                Acceptée
                            @elseif($serviceRequest->status === 'rejected')
                                Refusée
                            @else
                                {{ ucfirst($serviceRequest->status) }}
                            @endif
                        </span>
                    </div>
                    <div>
                        <label class="text-gray-600 text-sm font-semibold">Description</label>
                        <p class="text-gray-700 mt-2 whitespace-pre-line">{{ $serviceRequest->description }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations supplémentaires</h2>
                <div class="space-y-4">
                    @if($serviceRequest->event_date)
                        <div>
                            <label class="text-gray-600 text-sm font-semibold">Date prévue</label>
                            <p class="text-gray-800">{{ $serviceRequest->event_date->format('d/m/Y') }}</p>
                        </div>
                    @endif

                    @if($serviceRequest->location)
                        <div>
                            <label class="text-gray-600 text-sm font-semibold">Lieu</label>
                            <p class="text-gray-800">{{ $serviceRequest->location }}</p>
                        </div>
                    @endif

                    @if($serviceRequest->budget)
                        <div>
                            <label class="text-gray-600 text-sm font-semibold">Budget</label>
                            <p class="text-gray-800 font-bold">{{ number_format($serviceRequest->budget, 2) }}€</p>
                        </div>
                    @endif

                    @if($serviceRequest->provider)
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">Prestataire</h3>
                            <p class="text-gray-800">{{ $serviceRequest->provider->name }}</p>
                            @if($serviceRequest->provider->phone)
                                <p class="text-gray-600 mt-2">📞 {{ $serviceRequest->provider->phone }}</p>
                            @endif
                            @if($serviceRequest->provider->email)
                                <p class="text-gray-600">✉️ {{ $serviceRequest->provider->email }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
