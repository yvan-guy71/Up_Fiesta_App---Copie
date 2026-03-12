<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demande de Service - Up Fiesta</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <x-flash-messages />
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-10 w-auto">
            </a>
            <a href="{{ route('home') }}" class="text-sm font-bold text-indigo-600 hover:underline">← Retour à l'accueil</a>
            @include('partials.notifications')
        </nav>
    </header>

    <main class="max-w-3xl mx-auto py-12 px-4">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
            <h1 class="text-3xl font-black text-slate-900 mb-2">
                @if(request('type') === 'event')
                    Créer un projet avec Up-Fiesta
                @else
                    Exprimez vos besoins
                @endif
            </h1>
            <p class="text-slate-600 mb-8">
                @if(request('type') === 'event')
                    Décrivez votre projet (événement, travaux, service spécialisé...) et les types de professionnels souhaités. Up-Fiesta coordonnera tout pour vous.
                @else
                    Remplissez ce formulaire pour nous faire part de votre projet. L'équipe Up Fiesta vous mettra en relation avec les meilleurs professionnels.
                @endif
            </p>

            <form action="{{ route('service-requests.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="type" value="{{ request('type', 'service') }}">
                <input type="hidden" name="kind" value="{{ $kind ?? '' }}">
                
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Type de service</label>
                    <select name="kind" onchange="this.form.submit()" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all">
                        <option value="">Tous types</option>
                        <option value="{{ \App\Models\ServiceCategory::KIND_PRESTATIONS }}" {{ (isset($kind) && $kind === \App\Models\ServiceCategory::KIND_PRESTATIONS) ? 'selected' : '' }}>Prestations</option>
                        <option value="{{ \App\Models\ServiceCategory::KIND_DOMESTIQUES }}" {{ (isset($kind) && $kind === \App\Models\ServiceCategory::KIND_DOMESTIQUES) ? 'selected' : '' }}>Domestiques</option>
                    </select>
                </div>

                <div class="space-y-2">
                    @if(request('type') === 'event')
                        <label for="provider_ids" class="text-sm font-bold text-slate-700">Professionnels souhaités (optionnel)</label>
                        <select name="provider_ids[]" id="provider_ids" multiple class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all">
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">
                                    {{ $provider->name }} ({{ $provider->category->name }} - {{ $provider->city->name }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs professionnels.</p>
                        @error('provider_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @else
                        <label for="provider_id" class="text-sm font-bold text-slate-700">Professionnel ciblé (optionnel)</label>
                        <select name="provider_id" id="provider_id" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all">
                            <option value="">-- Aucun professionnel spécifique --</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ (isset($selectedProvider) && $selectedProvider->id == $provider->id) ? 'selected' : '' }}>
                                    {{ $provider->name }} ({{ $provider->category->name }} - {{ $provider->city->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('provider_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>

                <div class="space-y-2">
                    <label for="subject" class="text-sm font-bold text-slate-700">Objet de votre demande</label>
                    <input type="text" name="subject" id="subject" value="{{ isset($selectedProvider) ? 'Demande concernant ' . $selectedProvider->name : (old('subject') ?: (request('type') === 'event' ? "Organisation d'un événement" : '')) }}" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all" placeholder="Ex: Organisation d'un mariage, Location de salle...">
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="description" class="text-sm font-bold text-slate-700">Détails de vos besoins</label>
                    <textarea name="description" id="description" rows="5" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all resize-none" placeholder="Décrivez précisément ce que vous recherchez (type de travaux, besoins spécifiques, matériel...) et vos attentes.">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="event_date" class="text-sm font-bold text-slate-700">Date prévue (optionnel)</label>
                        <input type="datetime-local" name="event_date" id="event_date" value="{{ old('event_date') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all">
                        @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="location" class="text-sm font-bold text-slate-700">Lieu de l'événement (optionnel)</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all" placeholder="Ex: Lomé, Kpalimé...">
                        @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="budget" class="text-sm font-bold text-slate-700">Budget estimé (XOF - optionnel)</label>
                    <input type="number" name="budget" id="budget" value="{{ old('budget') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 outline-none transition-all" placeholder="Ex: 500000">
                    @error('budget') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
                        Envoyer ma demande à Up-Fiasta
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
