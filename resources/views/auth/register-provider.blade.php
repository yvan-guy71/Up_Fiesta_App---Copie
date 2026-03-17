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
    <meta name="theme-color" content="#4f46e5">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            border: 1px solid #e2e8f0 !important;
            background-color: white !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 2px #e0e7ff !important;
        }
        .ts-dropdown {
            border-radius: 0.75rem !important;
            margin-top: 0.25rem !important;
            border: 1px solid #e2e8f0 !important;
        }
        .ts-dropdown .active {
            background-color: #4f46e5 !important;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-slate-100">
            <div class="text-center">
                <a href="/" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-12 w-auto">
                </a>
                <h2 class="text-3xl font-black text-slate-900">Devenir Professionnel</h2>
                <p class="mt-2 text-slate-500">Rejoignez la plateforme n°1 des services et de l'événementiel au Togo</p>
            </div>

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="mt-8 space-y-8" action="{{ route('register.provider.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Informations personnelles -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 border-b pb-2">Informations du responsable</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 mb-1">Nom complet <span class="text-rose-500">*</span></label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="Votre nom complet">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-1">Email professionnel <span class="text-rose-500">*</span></label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="contact@entreprise.tg">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cni_number" class="block text-sm font-bold text-slate-700 mb-1">Numéro CNI <span class="text-rose-500">*</span></label>
                            <input id="cni_number" name="cni_number" type="text" required value="{{ old('cni_number') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="Numéro de votre CNI">
                        </div>
                        <div>
                            <label for="years_of_experience" class="block text-sm font-bold text-slate-700 mb-1">Années d'expérience <span class="text-rose-500">*</span></label>
                            <input id="years_of_experience" name="years_of_experience" type="number" required min="0" max="70" value="{{ old('years_of_experience', 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="0">
                        </div>
                        <div class="space-y-4 md:col-span-2">
                            <div>
                                <label for="cni_photo_front" class="block text-sm font-bold text-slate-700 mb-1">CNI (Recto) <span class="text-rose-500">*</span></label>
                                <input id="cni_photo_front" name="cni_photo_front" type="file" required class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div>
                                <label for="cni_photo_back" class="block text-sm font-bold text-slate-700 mb-1">CNI (Verso) <span class="text-rose-500">*</span></label>
                                <input id="cni_photo_back" name="cni_photo_back" type="file" required class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations entreprise -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 border-b pb-2">Détails du service</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="business_name" class="block text-sm font-bold text-slate-700 mb-1">Nom de l'entreprise <span class="text-rose-500">*</span></label>
                            <input id="business_name" name="business_name" type="text" required value="{{ old('business_name') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="Ex: Lumina Events">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-bold text-slate-700 mb-1">Téléphone (WhatsApp) <span class="text-rose-500">*</span></label>
                            <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                            <input type="hidden" name="full_phone" id="full_phone">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="service_kind" class="block text-sm font-bold text-slate-700 mb-1">Type de service <span class="text-rose-500">*</span></label>
                            <select id="service_kind" name="service_kind" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all appearance-none bg-white">
                                <option value="">Choisir un type...</option>
                                <option value="{{ App\Models\ServiceCategory::KIND_PRESTATIONS }}" {{ old('service_kind') == App\Models\ServiceCategory::KIND_PRESTATIONS ? 'selected' : '' }}>{{ __('messages.categories.kind_prestations') }} (Événementiel, etc.)</option>
                                <option value="{{ App\Models\ServiceCategory::KIND_DOMESTIQUES }}" {{ old('service_kind') == App\Models\ServiceCategory::KIND_DOMESTIQUES ? 'selected' : '' }}>{{ __('messages.categories.kind_domestiques') }} (Maison, Travaux, etc.)</option>
                            </select>
                        </div>
                        <div id="category_container" class="{{ old('service_kind') ? '' : 'hidden' }}">
                            <label for="category_ids" class="block text-sm font-bold text-slate-700 mb-1">Catégories spécifiques <span class="text-rose-500">*</span></label>
                            <select id="category_ids" name="category_ids[]" multiple required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all appearance-none bg-white">
                                @php
                                    $prestations = $categories->where('kind', App\Models\ServiceCategory::KIND_PRESTATIONS);
                                    $domestiques = $categories->where('kind', App\Models\ServiceCategory::KIND_DOMESTIQUES);
                                @endphp
                                <optgroup label="{{ __('messages.categories.kind_prestations') }}" data-kind="{{ App\Models\ServiceCategory::KIND_PRESTATIONS }}">
                                    @foreach($prestations as $category)
                                        <option value="{{ $category->id }}" {{ (is_array(old('category_ids')) && in_array($category->id, old('category_ids'))) ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('messages.categories.kind_domestiques') }}" data-kind="{{ App\Models\ServiceCategory::KIND_DOMESTIQUES }}">
                                    @foreach($domestiques as $category)
                                        <option value="{{ $category->id }}" {{ (is_array(old('category_ids')) && in_array($category->id, old('category_ids'))) ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="city_id" class="block text-sm font-bold text-slate-700 mb-1">Ville <span class="text-rose-500">*</span></label>
                            <select id="city_id" name="city_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all appearance-none bg-white">
                                <option value="">Choisir...</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="base_price" class="block text-sm font-bold text-slate-700 mb-1">Prix minimum (XOF) <span class="text-rose-500">*</span></label>
                            <input id="base_price" name="base_price" type="number" required value="{{ old('base_price') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="50000">
                        </div>
                        <div>
                            <label for="price_range_max" class="block text-sm font-bold text-slate-700 mb-1">Prix maximum (XOF) <span class="text-rose-500">*</span></label>
                            <input id="price_range_max" name="price_range_max" type="number" required value="{{ old('price_range_max') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="500000">
                        </div>
                    </div>
                    <div>
                        <label for="logo" class="block text-sm font-bold text-slate-700 mb-1">Logo / Photo de profil <span class="text-rose-500">*</span></label>
                        <input id="logo" name="logo" type="file" required class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-700 mb-1">Description de vos services <span class="text-rose-500">*</span></label>
                        <textarea id="description" name="description" required rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="Décrivez votre savoir-faire, votre expérience...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- Fiabilité Entreprise (Optionnel) -->
                <div class="space-y-4 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" id="is_company" name="is_company" value="1" {{ old('is_company') ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded-lg border-slate-300 focus:ring-indigo-500">
                        <label for="is_company" class="text-sm font-black text-slate-700 uppercase tracking-wider">Je suis une entreprise enregistrée</label>
                    </div>
                    <div id="company_fields" class="{{ old('is_company') ? '' : 'hidden' }} space-y-4 mt-4">
                        <div>
                            <label for="company_registration_number" class="block text-sm font-bold text-slate-700 mb-1">Numéro RCCM / NIF <span id="rccm_required" class="text-rose-500 hidden">*</span></label>
                            <input id="company_registration_number" name="company_registration_number" type="text" value="{{ old('company_registration_number') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="TG-LOM-XXXX">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="company_proof_doc_front" class="block text-sm font-bold text-slate-700 mb-1">Preuve d'enregistrement (Recto / Page 1) <span id="proof_required" class="text-rose-500 hidden">*</span></label>
                                <input id="company_proof_doc_front" name="company_proof_doc_front" type="file" class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div>
                                <label for="company_proof_doc_back" class="block text-sm font-bold text-slate-700 mb-1">Preuve d'enregistrement (Verso / Page 2) <span id="proof_back_required" class="text-rose-500 hidden">*</span></label>
                                <input id="company_proof_doc_back" name="company_proof_doc_back" type="file" class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 border-b pb-2">Sécurité du compte</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-1">Mot de passe <span class="text-rose-500">*</span></label>
                            <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="••••••••">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-1">Confirmer <span class="text-rose-500">*</span></label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-100 transition-all">
                        Créer mon compte professionnel
                    </button>
                </div>
            </form>

            <div class="text-center pt-6 border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    Déjà inscrit ? 
                    <a href="{{ route('filament.provider.auth.login') }}" class="font-bold text-indigo-600 hover:text-indigo-500">Se connecter</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Initialize Tom Select
        document.addEventListener('DOMContentLoaded', () => {
            // Extract options BEFORE Tom Select initialization to avoid losing them
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
