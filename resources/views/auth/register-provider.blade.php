<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devenir Professionnel - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#004aad">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { scroll-behavior: smooth; }
        
        body {
            background: #f9fafb;
        }

        .hero-section {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .form-group {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .benefit-card {
            transition: all 0.3s ease;
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }

        .benefit-card:hover {
            transform: translateY(-2px);
            border-color: #d1d5db;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            border: 1px solid #e5e7eb !important;
            background-color: white !important;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #0066ff !important;
            box-shadow: 0 0 0 2px rgba(0, 102, 255, 0.1) !important;
        }

        .ts-dropdown {
            border-radius: 0.75rem !important;
            margin-top: 0.25rem !important;
            border: 1px solid #e5e7eb !important;
        }

        .ts-dropdown .active {
            background-color: #dbeafe !important;
            color: #0066ff !important;
        }

        input:focus, textarea:focus, select:focus {
            transition: all 0.3s ease;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            background: linear-gradient(135deg, #0066ff, #0052cc);
        }

        .submit-btn {
            background: linear-gradient(135deg, #0066ff, #0052cc);
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #0052cc, #003d99);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="font-sans text-slate-900">
    <!-- Hero Section -->
    <div class="hero-section relative py-16 sm:py-20 text-slate-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <a href="/" class="inline-flex items-center gap-2 mb-8 hover:opacity-70 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-12 w-auto">
                </a>
                <h1 class="text-4xl sm:text-5xl font-black mb-6 leading-tight text-slate-900">
                    Devenez Professionnel
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto mb-8">
                    Rejoignez la plateforme n°1 des services et de l'événementiel au Togo. Trouvez des clients, développez votre activité et gagnez au quotidien.
                </p>
            </div>

            <!-- Benefits Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-12">
                <div class="benefit-card p-6 text-center">
                    <div class="text-3xl mb-4 text-blue-600"><i class="fas fa-star"></i></div>
                    <h3 class="font-bold text-slate-900 mb-2">Accès Gratuit</h3>
                    <p class="text-slate-600 text-sm">Zéro frais d'inscription, zéro coût caché</p>
                </div>
                <div class="benefit-card p-6 text-center">
                    <div class="text-3xl mb-4 text-blue-600"><i class="fas fa-users"></i></div>
                    <h3 class="font-bold text-slate-900 mb-2">Des Milliers de Clients</h3>
                    <p class="text-slate-600 text-sm">Accédez à une large base de clients qualifiés</p>
                </div>
                <div class="benefit-card p-6 text-center">
                    <div class="text-3xl mb-4 text-blue-600"><i class="fas fa-handshake"></i></div>
                    <h3 class="font-bold text-slate-900 mb-2">Relations Justes</h3>
                    <p class="text-slate-600 text-sm">Négociez vos tarifs et conditions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="py-12 sm:py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-8 bg-rose-50 border-l-4 border-rose-500 rounded-lg p-6 shadow-lg shadow-rose-100">
                    <div class="flex items-start gap-4">
                        <div class="text-2xl"></div>
                        <div>
                            <h3 class="font-bold text-rose-900 mb-3">Des erreurs ont été détectées:</h3>
                            <ul class="space-y-2">
                                @foreach ($errors->all() as $error)
                                    <li class="text-rose-700 text-sm">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200 border border-slate-100 overflow-hidden">
                <form class="p-8 sm:p-12 space-y-8" action="{{ route('register.provider.post') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Informations personnelles -->
                    <div class="form-group">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="section-icon"><i class="fas fa-user"></i></div>
                            <h2 class="text-2xl font-bold text-slate-900">Profil Personnel</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Nom complet <span class="text-rose-500">*</span></label>
                                <input id="name" name="name" type="text" required value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="Jean Dupont">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email professionnel <span class="text-rose-500">*</span></label>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="contact@entreprise.tg">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4">
                            <div>
                                <label for="cni_number" class="block text-sm font-bold text-slate-700 mb-2">Numéro CNI <span class="text-rose-500">*</span></label>
                                <input id="cni_number" name="cni_number" type="text" required value="{{ old('cni_number') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="0123456789">
                            </div>
                            <div>
                                <label for="years_of_experience" class="block text-sm font-bold text-slate-700 mb-2">Années d'expérience <span class="text-rose-500">*</span></label>
                                <input id="years_of_experience" name="years_of_experience" type="number" required min="0" max="70" value="{{ old('years_of_experience', 0) }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                            <p class="text-xs font-bold text-blue-900 mb-3">PIÈCES D'IDENTITÉ REQUISES</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cni_photo_front" class="block text-sm font-bold text-slate-700 mb-2">CNI (Recto) <span class="text-rose-500">*</span></label>
                                    <input id="cni_photo_front" name="cni_photo_front" type="file" required class="w-full px-4 py-2 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                </div>
                                <div>
                                    <label for="cni_photo_back" class="block text-sm font-bold text-slate-700 mb-2">CNI (Verso) <span class="text-rose-500">*</span></label>
                                    <input id="cni_photo_back" name="cni_photo_back" type="file" required class="w-full px-4 py-2 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations service -->
                    <div class="form-group border-t pt-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                            <h2 class="text-2xl font-bold text-slate-900">Votre Service</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="business_name" class="block text-sm font-bold text-slate-700 mb-2">Nom de l'entreprise <span class="text-rose-500">*</span></label>
                                <input id="business_name" name="business_name" type="text" required value="{{ old('business_name') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="Ex: Lumina Events">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Téléphone (WhatsApp) <span class="text-rose-500">*</span></label>
                                <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                                <input type="hidden" name="full_phone" id="full_phone">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4">
                            <div>
                                <label for="service_kind" class="block text-sm font-bold text-slate-700 mb-2">Type de service <span class="text-rose-500">*</span></label>
                                <!-- Only event services (Prestations) -->
                                <input type="hidden" name="service_kind" value="{{ App\Models\ServiceCategory::KIND_PRESTATIONS }}">
                                <div class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-700 font-semibold">
                                    {{ __('messages.categories.kind_prestations') }} (Événementiel)
                                </div>
                            </div>
                            <div id="category_container">
                                <label for="category_ids" class="block text-sm font-bold text-slate-700 mb-2">Catégories spécifiques <span class="text-rose-500">*</span></label>
                                <select id="category_ids" name="category_ids[]" multiple required class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all appearance-none bg-white">
                                    @php
                                        $prestations = $categories->where('kind', App\Models\ServiceCategory::KIND_PRESTATIONS);
                                    @endphp
                                    @foreach($prestations as $category)
                                        <option value="{{ $category->id }}" {{ (is_array(old('category_ids')) && in_array($category->id, old('category_ids'))) ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4">
                            <div>
                                <label for="city_id" class="block text-sm font-bold text-slate-700 mb-2">Ville <span class="text-rose-500">*</span></label>
                                <select id="city_id" name="city_id" required class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all appearance-none bg-white">
                                    <option value="">Choisir...</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="logo" class="block text-sm font-bold text-slate-700 mb-2">Logo / Photo de profil <span class="text-rose-500">*</span></label>
                                <input id="logo" name="logo" type="file" required class="w-full px-4 py-2 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4">
                            <div>
                                <label for="base_price" class="block text-sm font-bold text-slate-700 mb-2">Prix minimum (XOF) <span class="text-rose-500">*</span></label>
                                <input id="base_price" name="base_price" type="number" required value="{{ old('base_price') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="50000">
                            </div>
                            <div>
                                <label for="price_range_max" class="block text-sm font-bold text-slate-700 mb-2">Prix maximum (XOF) <span class="text-rose-500">*</span></label>
                                <input id="price_range_max" name="price_range_max" type="number" required value="{{ old('price_range_max') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="500000">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="description" class="block text-sm font-bold text-slate-700 mb-2">Description de vos services <span class="text-rose-500">*</span></label>
                            <textarea id="description" name="description" required rows="4" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all resize-none" placeholder="Décrivez votre savoir-faire, votre expérience, vos spécialités...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Entreprise enregistrée -->
                    <div class="form-group border-t pt-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="section-icon"><i class="fas fa-file-contract"></i></div>
                            <h2 class="text-2xl font-bold text-slate-900">Statut Juridique (Optionnel)</h2>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-6 rounded-2xl">
                            <label class="flex items-center gap-4 cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" id="is_company" name="is_company" value="1" {{ old('is_company') ? 'checked' : '' }} class="w-6 h-6 text-blue-600 rounded-lg border-2 border-slate-300 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Je suis une entreprise enregistrée</p>
                                    <p class="text-sm text-slate-600">Augmentez votre crédibilité avec une preuve d'enregistrement</p>
                                </div>
                            </label>
                            <div id="company_fields" class="{{ old('is_company') ? '' : 'hidden' }} space-y-4 mt-6 pt-6 border-t border-slate-200">
                                <div>
                                    <label for="company_registration_number" class="block text-sm font-bold text-slate-700 mb-2">Numéro RCCM / NIF <span id="rccm_required" class="text-rose-500 hidden">*</span></label>
                                    <input id="company_registration_number" name="company_registration_number" type="text" value="{{ old('company_registration_number') }}" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="TG-LOM-XXXX">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="company_proof_doc_front" class="block text-sm font-bold text-slate-700 mb-2">Preuve d'enregistrement (Recto) <span id="proof_required" class="text-rose-500 hidden">*</span></label>
                                        <input id="company_proof_doc_front" name="company_proof_doc_front" type="file" class="w-full px-4 py-2 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                    </div>
                                    <div>
                                        <label for="company_proof_doc_back" class="block text-sm font-bold text-slate-700 mb-2">Preuve d'enregistrement (Verso) <span id="proof_back_required" class="text-rose-500 hidden">*</span></label>
                                        <input id="company_proof_doc_back" name="company_proof_doc_back" type="file" class="w-full px-4 py-2 rounded-xl border-2 border-slate-200 hover:border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sécurité -->
                    <div class="form-group border-t pt-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="section-icon"><i class="fas fa-lock"></i></div>
                            <h2 class="text-2xl font-bold text-slate-900">Sécurité du Compte</h2>
                        </div>
                        <p class="text-slate-600 text-sm mb-4">Choisissez un mot de passe fort pour sécuriser votre compte professionnel</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Mot de passe <span class="text-rose-500">*</span></label>
                                <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="••••••••">
                                <p class="text-xs text-slate-500 mt-2">Minimum 8 caractères, avec majuscules et chiffres</p>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Confirmer le mot de passe <span class="text-rose-500">*</span></label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t pt-8">
                        <button type="submit" class="submit-btn w-full flex justify-center py-4 px-4 border border-transparent text-lg font-bold rounded-2xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg shadow-blue-500/30 transition-all duration-300">
                             Créer mon Compte Professionnel
                        </button>
                        <p class="text-center text-xs text-slate-500 mt-4">
                            En vous inscrivant, vous acceptez nos <a href="{{ route('legal.cgu') }}" class="font-bold text-blue-600 hover:text-blue-700">conditions générales</a> et notre <a href="{{ route('legal.privacy') }}" class="font-bold text-blue-600 hover:text-blue-700">politique de confidentialité</a>
                        </p>
                    </div>
                </form>

                <!-- Footer -->
                <div class="bg-slate-50 px-8 sm:px-12 py-8 border-t border-slate-200">
                    <p class="text-center text-slate-600">
                        Déjà inscrit? <a href="{{ route('filament.provider.auth.login') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Se connecter à l'espace pro</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Initialize Tom Select
        document.addEventListener('DOMContentLoaded', () => {
            const categorySelectElement = document.getElementById('category_ids');
            const allOptions = Array.from(categorySelectElement.querySelectorAll('option')).map(opt => ({
                value: opt.value,
                text: opt.text,
                kind: opt.closest('optgroup') ? opt.closest('optgroup').dataset.kind : null
            }));

            const categorySelect = new TomSelect("#category_ids", {
                create: false,
                maxItems: 5,
                plugins: ['dropdown_input', 'remove_button'],
                placeholder: 'Rechercher des catégories...'
            });

            const citySelect = new TomSelect("#city_id", {
                create: false,
                maxItems: 1,
                plugins: ['dropdown_input'],
                placeholder: 'Rechercher une ville...'
            });

            // Logic for filtering categories based on service kind
            const serviceKindSelect = document.getElementById('service_kind');
            const categoryContainer = document.getElementById('category_container');

            function filterCategories() {
                const kind = serviceKindSelect.value;
                if (!kind) {
                    categoryContainer.classList.add('hidden');
                    categorySelect.clear();
                    categorySelect.clearOptions();
                    return;
                }

                categoryContainer.classList.remove('hidden');
                
                // Filter options
                const filteredOptions = allOptions.filter(opt => opt.kind === kind);
                
                categorySelect.clearOptions();
                categorySelect.addOptions(filteredOptions);
                categorySelect.refreshOptions(false);
            }

            serviceKindSelect.addEventListener('change', filterCategories);

            // Initial filter if old value exists
            if (serviceKindSelect.value) {
                filterCategories();
                // Restore selected values from old input if any
                @if(is_array(old('category_ids')))
                    categorySelect.setValue({!! json_encode(old('category_ids')) !!});
                @endif
            }
        });

        // International Telephone Input
        const phoneInput = document.querySelector("#phone");
        const fullPhoneInput = document.querySelector("#full_phone");
        
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "tg", // Togo par défaut
            preferredCountries: ["tg", "bj", "gh", "ci", "fr"],
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
        });

        // Mettre à jour le numéro complet avant la soumission
        phoneInput.addEventListener('change', () => {
            fullPhoneInput.value = iti.getNumber();
        });
        
        phoneInput.addEventListener('keyup', () => {
            fullPhoneInput.value = iti.getNumber();
        });

        // Toggle Company Fields
        document.getElementById('is_company').addEventListener('change', function() {
            const fields = document.getElementById('company_fields');
            const rccmRequired = document.getElementById('rccm_required');
            const proofRequired = document.getElementById('proof_required');
            const proofBackRequired = document.getElementById('proof_back_required');
            const rccmInput = document.getElementById('company_registration_number');
            const proofFrontInput = document.getElementById('company_proof_doc_front');
            const proofBackInput = document.getElementById('company_proof_doc_back');
            
            if (this.checked) {
                fields.classList.remove('hidden');
                rccmRequired.classList.remove('hidden');
                proofRequired.classList.remove('hidden');
                proofBackRequired.classList.remove('hidden');
                rccmInput.setAttribute('required', 'required');
                proofFrontInput.setAttribute('required', 'required');
                proofBackInput.setAttribute('required', 'required');
            } else {
                fields.classList.add('hidden');
                rccmRequired.classList.add('hidden');
                proofRequired.classList.add('hidden');
                proofBackRequired.classList.add('hidden');
                rccmInput.removeAttribute('required');
                proofFrontInput.removeAttribute('required');
                proofBackInput.removeAttribute('required');
            }
        });

        // Initialize on page load in case of old input
        window.addEventListener('load', function() {
            const isCompany = document.getElementById('is_company');
            if (isCompany.checked) {
                isCompany.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
