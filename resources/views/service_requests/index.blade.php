@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2">Mes Demandes</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Suivez l'état de vos demandes de services et événements en temps réel.</p>
            </div>
            <a href="{{ route('service-requests.create') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-200 dark:shadow-none transition-all duration-200 hover:scale-105 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle demande
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Total</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $requests->total() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                <p class="text-xs font-black text-amber-400 uppercase tracking-widest mb-1">En attente</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $requests->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                <p class="text-xs font-black text-emerald-400 uppercase tracking-widest mb-1">Confirmées</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $requests->where('status', 'assigned')->count() }}</p>
            </div>
        </div>

        <!-- Requests List -->
        @if($requests->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-12 text-center border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none">
                <div class="w-24 h-24 bg-primary-50 dark:bg-primary-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Aucune demande pour le moment</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Exprimez vos besoins et laissez-nous vous trouver les meilleurs prestataires.</p>
                <a href="{{ route('service-requests.create') }}" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">Commencer ici &rarr;</a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($requests as $request)
                    <div class="group bg-white dark:bg-slate-900 rounded-[2.5rem] p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-primary-500/10 dark:hover:shadow-none transition-all duration-300 relative overflow-hidden">
                        <!-- Decorative Gradient -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-500/5 to-purple-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                        
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest 
                                        {{ $request->type === 'event' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ $request->type === 'event' ? 'Événement' : 'Service' }}
                                    </span>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <span class="text-xs font-bold text-slate-400">#{{ $request->id }}</span>
                                    <span class="text-slate-300 dark:text-slate-700">•</span>
                                    <span class="text-xs font-bold text-slate-400">{{ $request->created_at->format('d M Y') }}</span>
                                </div>
                                <h2 class="text-2xl font-black text-slate-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors mb-3">
                                    {{ $request->subject }}
                                </h2>
                                <p class="text-slate-500 dark:text-slate-400 line-clamp-2 font-medium mb-6">
                                    {{ $request->description }}
                                </p>
                                
                                <div class="flex flex-wrap items-center gap-6 text-sm">
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </div>
                                        <span class="font-bold">{{ $request->location }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                        <span class="font-bold">{{ $request->event_date->format('d/m/Y') }}</span>
                                    </div>
                                    @if($request->budget)
                                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <span class="font-black">{{ number_format($request->budget, 0, ',', ' ') }} CFA</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-4 lg:w-72">
                                <div class="w-full">
                                    <span class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 text-center lg:text-left">Statut actuel</span>
                                    <div class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-2xl text-sm font-black uppercase tracking-wider
                                        @if($request->status === 'pending') bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/50
                                        @elseif($request->status === 'assigned') bg-primary-50 text-primary-600 border border-primary-100 dark:bg-primary-900/20 dark:text-primary-400 dark:border-primary-800/50
                                        @elseif($request->status === 'completed') bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/50
                                        @else bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700
                                        @endif">
                                        @if($request->status === 'pending')
                                            <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                                            En attente
                                        @elseif($request->status === 'assigned')
                                            <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                                            Assignée
                                        @elseif($request->status === 'completed')
                                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                            Terminée
                                        @else
                                            {{ ucfirst($request->status) }}
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('service-requests.show', $request) }}" class="w-full flex items-center justify-center px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-2xl hover:bg-primary-600 dark:hover:bg-primary-500 dark:hover:text-white transition-all duration-200">
                                    Détails
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

