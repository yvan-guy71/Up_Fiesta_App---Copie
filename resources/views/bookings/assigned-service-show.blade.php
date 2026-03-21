@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('bookings.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour à mes réservations
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg dark:shadow-slate-900 p-8 border dark:border-slate-700">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Assignation #{{ $assignedService->id }}</h1>
            <p class="text-gray-500 dark:text-slate-400 mt-2">Assignée le {{ $assignedService->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column: Request Details -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Détails du service</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Sujet de la demande</label>
                        <p class="text-gray-800 dark:text-slate-200 text-lg font-semibold">{{ $assignedService->serviceRequest->subject }}</p>
                    </div>
                    
                    <div>
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Description</label>
                        <p class="text-gray-700 dark:text-slate-300 mt-2 whitespace-pre-line">{{ $assignedService->serviceRequest->description }}</p>
                    </div>

                    @if($assignedService->serviceRequest->event_date)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Date prévue</label>
                            <p class="text-gray-800 dark:text-slate-200">{{ $assignedService->serviceRequest->event_date->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif

                    @if($assignedService->serviceRequest->location)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Lieu</label>
                            <p class="text-gray-800 dark:text-slate-200">{{ $assignedService->serviceRequest->location }}</p>
                        </div>
                    @endif

                    @if($assignedService->serviceRequest->budget)
                        <div>
                            <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Budget</label>
                            <p class="text-gray-800 dark:text-slate-200 font-bold text-lg">{{ number_format($assignedService->serviceRequest->budget, 0, ',', ' ') }} CFA</p>
                        </div>
                    @endif

                    <div>
                        <label class="text-gray-600 dark:text-slate-400 text-sm font-semibold">Statut de l'assignation</label>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                            @if($assignedService->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @elseif($assignedService->status === 'accepted') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @elseif($assignedService->status === 'rejected') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @elseif($assignedService->status === 'completed') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                            @else bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200
                            @endif">
                            @if($assignedService->status === 'pending')
                                En attente
                            @elseif($assignedService->status === 'accepted')
                                Acceptée
                            @elseif($assignedService->status === 'rejected')
                                Refusée
                            @elseif($assignedService->status === 'completed')
                                Complétée
                            @else
                                {{ ucfirst($assignedService->status) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Provider Info -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Prestataire</h2>
                <div class="bg-gray-50 dark:bg-slate-700 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        @if($assignedService->provider->logo)
                            <img src="{{ asset('storage/' . $assignedService->provider->logo) }}" alt="{{ $assignedService->provider->name }}" class="h-16 w-16 object-cover rounded-full mr-4 border-2 border-indigo-200">
                        @else
                            <div class="h-16 w-16 bg-indigo-100 dark:bg-indigo-900 rounded-full mr-4 flex items-center justify-center border-2 border-indigo-200 dark:border-indigo-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $assignedService->provider->name }}</h3>
                            @if($assignedService->provider->category)
                                <p class="text-sm text-gray-600 dark:text-slate-400">{{ $assignedService->provider->category->name }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if($assignedService->provider->email)
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                <p class="text-gray-700 dark:text-slate-300">{{ $assignedService->provider->email }}</p>
                            </div>
                        @endif

                        @if($assignedService->provider->city)
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-slate-300">{{ $assignedService->provider->city->name }}</p>
                            </div>
                        @endif

                        @if($assignedService->provider->years_of_experience)
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <p class="text-gray-700 dark:text-slate-300">{{ $assignedService->provider->years_of_experience }} ans d'expérience</p>
                            </div>
                        @endif
                    </div>

                    @if($assignedService->status === 'accepted')
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-slate-600">
                            <p class="text-sm text-gray-600 dark:text-slate-400 mb-4">Le prestataire a accepté votre demande. Vous pouvez maintenant créer une réservation pour finaliser le paiement.</p>
                            <form action="{{ route('bookings.createFromAssignedService', $assignedService->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-block px-6 py-3 bg-indigo-600 dark:bg-indigo-700 text-white font-semibold rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-800 transition-colors">
                                    Créer une réservation et payer
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
