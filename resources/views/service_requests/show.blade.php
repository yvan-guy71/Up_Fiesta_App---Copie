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
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                @if($serviceRequest->type === 'event')
                    <svg class="h-8 w-8 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                    </svg>
                @else
                    <svg class="h-8 w-8 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0L7.86 5.89a1 1 0 01-.95.69h-2.3a1 1 0 00-.96 1.36l.8 2.33a1 1 0 01-.36 1.25L3.68 13.5a1 1 0 00.36 1.75h2.3a1 1 0 00.95-.69l.8-2.33a1 1 0 011.25-.36l2.33.8a1 1 0 001.36-.96v-2.3a1 1 0 01.69-.95l2.72-.68zM13.5 7.5a1 1 0 011 1v.5a1 1 0 001 1h.5a1 1 0 110 2h-.5a1 1 0 00-1 1v.5a1 1 0 11-2 0v-.5a1 1 0 00-1-1h-.5a1 1 0 110-2h.5a1 1 0 001-1v-.5a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                @endif
                {{ $serviceRequest->subject }}
            </h1>
            <p class="text-gray-500 dark:text-slate-400 mt-2">Demande #{{ $serviceRequest->id }} - Créée le {{ $serviceRequest->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Détails de la demande</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Type</label>
                        <p class="text-gray-800 dark:text-slate-200 capitalize flex items-center gap-2">
                            @if($serviceRequest->type === 'event')
                                <svg class="h-5 w-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                                </svg>
                                Événement
                            @else
                                <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0L7.86 5.89a1 1 0 01-.95.69h-2.3a1 1 0 00-.96 1.36l.8 2.33a1 1 0 01-.36 1.25L3.68 13.5a1 1 0 00.36 1.75h2.3a1 1 0 00.95-.69l.8-2.33a1 1 0 011.25-.36l2.33.8a1 1 0 001.36-.96v-2.3a1 1 0 01.69-.95l2.72-.68zM13.5 7.5a1 1 0 011 1v.5a1 1 0 001 1h.5a1 1 0 110 2h-.5a1 1 0 00-1 1v.5a1 1 0 11-2 0v-.5a1 1 0 00-1-1h-.5a1 1 0 110-2h.5a1 1 0 001-1v-.5a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Service
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Catégorie</label>
                        <p class="text-gray-800 dark:text-slate-200 capitalize flex items-center gap-2">
                            <svg class="h-5 w-5 text-sky-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                            </svg>
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
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-2 w-max
                            @if($serviceRequest->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @elseif($serviceRequest->status === 'approved') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @elseif($serviceRequest->status === 'rejected') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @else bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200
                            @endif">
                            @if($serviceRequest->status === 'pending')
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 10.586V6z" clip-rule="evenodd"/>
                                </svg>
                                En attente
                            @elseif($serviceRequest->status === 'approved')
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Acceptée
                            @elseif($serviceRequest->status === 'rejected')
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
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
                            <p class="text-gray-800 dark:text-slate-200 flex items-center gap-2">
                                <svg class="h-5 w-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $serviceRequest->event_date->format('d/m/Y') }}
                            </p>
                        </div>
                    @endif

                    @if($serviceRequest->location)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Lieu</label>
                            <p class="text-gray-800 dark:text-slate-200 flex items-center gap-2">
                                <svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                {{ $serviceRequest->location }}
                            </p>
                        </div>
                    @endif

                    @if($serviceRequest->budget)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Budget</label>
                            <p class="text-gray-800 dark:text-slate-200 font-bold flex items-center gap-2">
                                <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.5 2.5 0 00-1.134.658 1 1 0 00-.21 1.418.999.999 0 001.417.21a2.5 2.5 0 001.333-2.335V6.5a1 1 0 011-1h1.5a1 1 0 110 2h-1v1.518a2.5 2.5 0 001.134.658 1 1 0 00.21 1.418.999.999 0 001.417-.21 2.5 2.5 0 001.333-2.335V8.5h1.5a1 1 0 110 2h-1.5a1 1 0 01-1-1v-1.05a2.5 2.5 0 00-1.134-.658 1 1 0 00-.21-1.418.999.999 0 00-1.417.21 2.5 2.5 0 00-1.333 2.335V12.5a1 1 0 01-1 1H8a1 1 0 110-2h1v-1.518a2.5 2.5 0 00-1.134-.658 1 1 0 00-.21-1.418z"/>
                                </svg>
                                {{ number_format($serviceRequest->budget, 0, ',', ' ') }} CFA
                            </p>
                        </div>
                    @endif

                    @if($serviceRequest->provider)
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800/50">
                            <h3 class="font-semibold text-gray-800 dark:text-white mb-2 flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                </svg>
                                Prestataire
                            </h3>
                            <p class="text-gray-800 dark:text-slate-200">{{ $serviceRequest->provider->name }}</p>
                            @if($serviceRequest->provider->phone)
                                <p class="text-gray-600 dark:text-slate-400 mt-2 flex items-center gap-2">
                                    <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    {{ $serviceRequest->provider->phone }}
                                </p>
                            @endif
                            @if($serviceRequest->provider->email)
                                <p class="text-gray-600 dark:text-slate-400 flex items-center gap-2">
                                    <svg class="h-4 w-4 text-sky-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    {{ $serviceRequest->provider->email }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
