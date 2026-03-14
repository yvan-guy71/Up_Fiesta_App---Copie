@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('service-requests.create') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour aux demandes
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg dark:shadow-slate-900 p-8 border dark:border-slate-700">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $serviceRequest->subject }}</h1>
            <p class="text-gray-500 dark:text-slate-400 mt-2">Demande #{{ $serviceRequest->id }} - Créée le {{ $serviceRequest->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Détails de la demande</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Type</label>
                        <p class="text-gray-800 dark:text-slate-200 capitalize">
                            @if($serviceRequest->type === 'event')
                                📅 Événement
                            @else
                                🔧 Service
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Catégorie</label>
                        <p class="text-gray-800 dark:text-slate-200 capitalize">
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
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Statut</label>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                            @if($serviceRequest->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @elseif($serviceRequest->status === 'approved') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @elseif($serviceRequest->status === 'rejected') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @else bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200
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
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Description</label>
                        <p class="text-gray-700 dark:text-slate-300 mt-2 whitespace-pre-line">{{ $serviceRequest->description }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Informations supplémentaires</h2>
                <div class="space-y-4">
                    @if($serviceRequest->event_date)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Date prévue</label>
                            <p class="text-gray-800 dark:text-slate-200">{{ $serviceRequest->event_date->format('d/m/Y') }}</p>
                        </div>
                    @endif

                    @if($serviceRequest->location)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Lieu</label>
                            <p class="text-gray-800 dark:text-slate-200">{{ $serviceRequest->location }}</p>
                        </div>
                    @endif

                    @if($serviceRequest->budget)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Budget</label>
                            <p class="text-gray-800 dark:text-slate-200 font-bold">{{ number_format($serviceRequest->budget, 2) }}€</p>
                        </div>
                    @endif

                    @if($serviceRequest->provider)
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800/50">
                            <h3 class="font-semibold text-gray-800 dark:text-white mb-2">Prestataire</h3>
                            <p class="text-gray-800 dark:text-slate-200">{{ $serviceRequest->provider->name }}</p>
                            @if($serviceRequest->provider->phone)
                                <p class="text-gray-600 dark:text-slate-400 mt-2">📞 {{ $serviceRequest->provider->phone }}</p>
                            @endif
                            @if($serviceRequest->provider->email)
                                <p class="text-gray-600 dark:text-slate-400">✉️ {{ $serviceRequest->provider->email }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
