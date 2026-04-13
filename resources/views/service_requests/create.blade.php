<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ request('type') === 'event' ? "Organisation d'Événement" : 'Demande de Service' }} - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#004aad">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = { darkMode: 'class' };
        </script>
    @endif
    @include('partials.dark-mode-styles')
    <style>
        select option {
            background-color: white;
            color: #1e293b;
        }
        select option:checked {
            background-color: #e0e7ff;
            color: #312e81;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900 font-sans text-slate-900 dark:text-slate-100">
    <x-flash-messages />
    @include('partials.header')

    <main class="max-w-5xl mx-auto py-16 px-4">
        @if(request('type') === 'event')
            <!-- EVENT ORGANIZATION FORM -->
            <div class="mb-12">
                <div class="inline-flex items-center gap-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-4 py-2 rounded-full text-xs font-bold mb-4">
                    <i class="fas fa-calendar-check"></i> ORGANISATION D'ÉVÉNEMENT
                </div>
                <h1 class="text-5xl font-black text-slate-900 dark:text-white mb-4 leading-tight">
                    Confiez-nous votre événement
                </h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl leading-relaxed">
                    Décrivez votre événement et vos besoins. Up Fiesta vous proposera les meilleurs prestataires.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="h-3 bg-gradient-to-r from-blue-600 to-blue-700"></div>
                
                <form action="{{ route('service-requests.store') }}" method="POST" class="p-10 md:p-14 space-y-10">
                    @csrf
                    <input type="hidden" name="type" value="event">
                    <input type="hidden" name="kind" value="{{ \App\Models\ServiceCategory::KIND_PRESTATIONS }}">

                    <!-- SECTION 1 -->
                    <div>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="flex items-center justify-center w-9 h-9 bg-blue-600 text-white rounded-full font-black text-sm">1</div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white">Détails de l'événement</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="subject" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                    <span class="text-blue-600">★</span> Type d'événement
                                </label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject', "Organisation d'un événement") }}" required placeholder="Mariage • Conférence • Anniversaire • Baptême • Séminaire..." class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500 text-lg">
                                @error('subject') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="event_date" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                    <span class="text-blue-600">★</span> Date prévue
                                </label>
                                <input type="datetime-local" name="event_date" id="event_date" value="{{ old('event_date') }}" required class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                @error('event_date') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                    <span class="text-blue-600">★</span> Lieu
                                </label>
                                <input type="text" name="location" id="location" value="{{ old('location') }}" required placeholder="Lomé..." class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500">
                                @error('location') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                <span class="text-blue-600">★</span> Description détaillée
                            </label>
                            <textarea name="description" id="description" rows="7" required placeholder="Nombre de participants • Type de prestation • Ambiance souhaitée • Exigences particulières..." class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none placeholder-slate-400 dark:placeholder-slate-500" style="line-height: 1.7;">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="bg-slate-100 dark:bg-slate-700/30 h-px"></div>

                    <!-- SECTION 2 -->
                    <div>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="flex items-center justify-center w-9 h-9 bg-blue-600 text-white rounded-full font-black text-sm">2</div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white">Budget et prestataires</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="budget" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                    <span class="text-blue-600">★</span> Budget estimé (XOF)
                                </label>
                                <input type="number" name="budget" id="budget" value="{{ old('budget') }}" required min="10000" step="5000" placeholder="500000" class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500 text-lg">
                                @error('budget') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="professional_count" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                    <span class="text-blue-600">★</span> Nombre de prestataires
                                </label>
                                <select name="professional_count" id="professional_count" required class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all appearance-none text-lg">
                                    <option value="">-- Sélectionnez --</option>
                                    <option value="1">1 coordinateur principal</option>
                                    <option value="2-3">2-3 prestataires</option>
                                    <option value="4-5">4-5 prestataires</option>
                                    <option value="6+" value="6+">6 ou plus</option>
                                </select>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Up Fiesta proposera les meilleurs prestataires.</p>
                                @error('professional_count') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="category_preferences" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                <i class="fas fa-lightbulb text-amber-500 mr-1"></i> Catégories de prestataires (optionnel)
                            </label>
                            <select name="category_preferences[]" id="category_preferences" multiple class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Ctrl/Cmd + Clic pour en sélectionner plusieurs.</p>
                        </div>
                    </div>

                    <div class="bg-slate-100 dark:bg-slate-700/30 h-px"></div>

                    <!-- SECTION 3 -->
                    <div>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="flex items-center justify-center w-9 h-9 bg-blue-600 text-white rounded-full font-black text-sm">3</div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white">Spécifications supplémentaires</h2>
                        </div>

                        <div>
                            <label for="special_requirements" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                Exigences spéciales (optionnel)
                            </label>
                            <textarea name="special_requirements" id="special_requirements" rows="4" placeholder="Langues requises • Délais spécifiques • Matériel à fournir • Préférences particulières..." class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none placeholder-slate-400 dark:placeholder-slate-500" style="line-height: 1.7;">{{ old('special_requirements') }}</textarea>
                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 rounded-2xl p-8 pt-6">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('home') }}" class="px-8 py-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-all text-center">
                                Annuler
                            </a>
                            <button type="submit" class="px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-black shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 transition-all transform hover:-translate-y-0.5 text-center">
                                <i class="fas fa-paper-plane mr-2"></i> Envoyer ma demande
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- INFO SECTION -->
            <div class="mt-16 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 rounded-3xl p-10 border-2 border-blue-200 dark:border-blue-800">
                <h3 class="text-2xl font-black text-blue-900 dark:text-blue-100 mb-8">
                    <i class="fas fa-lightbulb mr-2"></i> Comment fonctionne Up Fiesta ?
                </h3>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center font-black text-lg flex-none">1</div>
                        <div>
                            <h4 class="font-black text-blue-900 dark:text-blue-100 mb-2">Vous créez</h4>
                            <p class="text-sm text-blue-800 dark:text-blue-200">Une demande détaillée de votre événement</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center font-black text-lg flex-none">2</div>
                        <div>
                            <h4 class="font-black text-blue-900 dark:text-blue-100 mb-2">Nous séléctionnons</h4>
                            <p class="text-sm text-blue-800 dark:text-blue-200">Les meilleurs prestataires adaptés</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center font-black text-lg flex-none">3</div>
                        <div>
                            <h4 class="font-black text-blue-900 dark:text-blue-100 mb-2">Vous approuvez</h4>
                            <p class="text-sm text-blue-800 dark:text-blue-200">Confirmez la sélection et démarrez</p>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- DIRECT SERVICE REQUEST -->
            <div class="mb-12">
                <div class="inline-flex items-center gap-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-4 py-2 rounded-full text-xs font-bold mb-4">
                    <i class="fas fa-handshake"></i> DEMANDE DIRECTE
                </div>
                <h1 class="text-5xl font-black text-slate-900 dark:text-white mb-4 leading-tight">
                    Exprimez vos besoins
                </h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl leading-relaxed">
                    Contactez un prestataire ou envoyez une demande générale pour être mis en relation.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="h-3 bg-gradient-to-r from-purple-600 to-purple-700"></div>
                
                <form action="{{ route('service-requests.store') }}" method="POST" class="p-10 md:p-14 space-y-8">
                    @csrf
                    <input type="hidden" name="type" value="service">
                    <input type="hidden" name="kind" value="{{ \App\Models\ServiceCategory::KIND_PRESTATIONS }}">

                    <div>
                        <label for="provider_id" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                            Prestataire ciblé (optionnel)
                        </label>
                        <select name="provider_id" id="provider_id" class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all appearance-none">
                            <option value="">-- Aucun spécifique --</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }} ({{ $provider->category->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                            <span class="text-blue-600">★</span> Objet
                        </label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required placeholder="Décrivez brièvement votre besoin..." class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500 text-lg">
                        @error('subject') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                            <span class="text-blue-600">★</span> Détails
                        </label>
                        <textarea name="description" id="description" rows="6" required placeholder="Décrivez précisément vos besoins et attentes..." class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all resize-none placeholder-slate-400 dark:placeholder-slate-500" style="line-height: 1.7;">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="event_date" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                <span class="text-blue-600">★</span> Date prévue
                            </label>
                            <input type="datetime-local" name="event_date" id="event_date" value="{{ old('event_date') }}" required class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all">
                            @error('event_date') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                <span class="text-blue-600">★</span> Lieu
                            </label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" required placeholder="Lomé..." class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500">
                            @error('location') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="budget" class="block text-sm font-black text-slate-800 dark:text-slate-200 mb-3">
                                <span class="text-blue-600">★</span> Budget (XOF)
                            </label>
                            <input type="number" name="budget" id="budget" value="{{ old('budget') }}" required min="500" step="500" placeholder="50000" class="w-full bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-2xl px-6 py-4 text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500 text-lg">
                            @error('budget') <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 border-2 border-purple-200 dark:border-purple-800 rounded-2xl p-8 pt-6">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('home') }}" class="px-8 py-4 rounded-xl border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-all text-center">
                                Annuler
                            </a>
                            <button type="submit" class="px-8 py-4 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-black shadow-lg shadow-purple-500/30 hover:shadow-purple-600/40 transition-all transform hover:-translate-y-0.5 text-center">
                                <i class="fas fa-paper-plane mr-2"></i> Envoyer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </main>

    @include('partials.footer')
</body>
</html>

