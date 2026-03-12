<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('legal.privacy_title') }} - Up Fiesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Up Fiesta Logo" class="h-8 w-auto">
                </a>
                <a href="/" class="text-sm font-bold text-indigo-600 hover:text-indigo-500">{{ __('legal.back_home') }}</a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xl shadow-slate-200 border border-slate-100">
            <h1 class="text-4xl font-black text-slate-900 mb-2">{{ __('legal.privacy_title') }}</h1>
            <p class="text-sm text-slate-400 italic mb-8">{{ __('legal.last_update', ['date' => '04 Mars 2026']) }}</p>
            
            <div class="prose prose-slate max-w-none space-y-6">
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.engagement_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.engagement_p1') }}</p>
                </section>
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.collecte_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.collecte_intro') }}</p>
                    <h3 class="text-lg font-semibold mt-2">{{ __('legal_content.privacy.collecte_users_title') }}</h3>
                    <ul class="list-disc pl-6 text-slate-600 space-y-1">
                        <li>{{ __('legal_content.privacy.collecte_users_1') }}</li>
                        <li>{{ __('legal_content.privacy.collecte_users_2') }}</li>
                        <li>{{ __('legal_content.privacy.collecte_users_3') }}</li>
                        <li>{{ __('legal_content.privacy.collecte_users_4') }}</li>
                        <li>{{ __('legal_content.privacy.collecte_users_5') }}</li>
                    </ul>
                    <h3 class="text-lg font-semibold mt-4">{{ __('legal_content.privacy.collecte_providers_title') }}</h3>
                    <ul class="list-disc pl-6 text-slate-600 space-y-1">
                        <li>{{ __('legal_content.privacy.collecte_providers_1') }}</li>
                        <li>{{ __('legal_content.privacy.collecte_providers_2') }}</li>
                        <li>{{ __('legal_content.privacy.collecte_providers_3') }}</li>
                        <li>{{ __('legal_content.privacy.collecte_providers_4') }}</li>
                    </ul>
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-slate-700 mt-4">
                        <p class="font-semibold">{{ __('legal_content.privacy.note_sensitive_title') }}</p>
                        <p>{{ __('legal_content.privacy.note_sensitive_p') }}</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.utilisation_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.utilisation_intro') }}</p>
                    <ul class="list-disc pl-6 text-slate-600 space-y-2">
                        <li>{{ __('legal_content.privacy.utilisation_1') }}</li>
                        <li>{{ __('legal_content.privacy.utilisation_2') }}</li>
                        <li>{{ __('legal_content.privacy.utilisation_3') }}</li>
                        <li>{{ __('legal_content.privacy.utilisation_4') }}</li>
                        <li>{{ __('legal_content.privacy.utilisation_5') }}</li>
                        <li>{{ __('legal_content.privacy.utilisation_6') }}</li>
                        <li>{{ __('legal_content.privacy.utilisation_7') }}</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.partage_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.partage_intro') }}</p>
                    <ul class="list-disc pl-6 text-slate-600 space-y-2">
                        <li>{{ __('legal_content.privacy.partage_1') }}</li>
                        <li>{{ __('legal_content.privacy.partage_2') }}</li>
                        <li>{{ __('legal_content.privacy.partage_3') }}</li>
                        <li>{{ __('legal_content.privacy.partage_4') }}</li>
                    </ul>
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-slate-700 mt-4">
                        <p class="font-semibold">{{ __('legal_content.privacy.note_paiement_title') }}</p>
                        <p>{{ __('legal_content.privacy.note_paiement_p') }}</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.duree_title') }}</h2>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="py-2 pr-4 font-semibold text-slate-700">{{ __('legal_content.privacy.duree_th_type') }}</th>
                                <th class="py-2 font-semibold text-slate-700">{{ __('legal_content.privacy.duree_th_duration') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-600">
                            <tr>
                                <td class="py-1 pr-4">{{ __('legal_content.privacy.duree_row_account') }}</td>
                                <td class="py-1">{{ __('legal_content.privacy.duree_row_account_duration') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4">{{ __('legal_content.privacy.duree_row_bookings') }}</td>
                                <td class="py-1">{{ __('legal_content.privacy.duree_row_bookings_duration') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4">{{ __('legal_content.privacy.duree_row_iddocs') }}</td>
                                <td class="py-1">{{ __('legal_content.privacy.duree_row_iddocs_duration') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4">{{ __('legal_content.privacy.duree_row_payment') }}</td>
                                <td class="py-1">{{ __('legal_content.privacy.duree_row_payment_duration') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4">{{ __('legal_content.privacy.duree_row_cookies') }}</td>
                                <td class="py-1">{{ __('legal_content.privacy.duree_row_cookies_duration') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.securite_title') }}</h2>
                    <ul class="list-disc pl-6 text-slate-600 space-y-2">
                        <li>{{ __('legal_content.privacy.securite_1') }}</li>
                        <li>{{ __('legal_content.privacy.securite_2') }}</li>
                        <li>{{ __('legal_content.privacy.securite_3') }}</li>
                        <li>{{ __('legal_content.privacy.securite_4') }}</li>
                        <li>{{ __('legal_content.privacy.securite_5') }}</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.droits_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.droits_intro') }}</p>
                    <ul class="list-disc pl-6 text-slate-600 space-y-2">
                        <li>{{ __('legal_content.privacy.droits_1') }}</li>
                        <li>{{ __('legal_content.privacy.droits_2') }}</li>
                        <li>{{ __('legal_content.privacy.droits_3') }}</li>
                        <li>{{ __('legal_content.privacy.droits_4') }}</li>
                        <li>{{ __('legal_content.privacy.droits_5') }}</li>
                    </ul>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.droits_how') }}</p>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.droits_email_intro') }}</p>
                    <ul class="list-disc pl-6 text-slate-600 space-y-1">
                        <li>{{ __('legal_content.privacy.droits_req_1') }}</li>
                        <li>{{ __('legal_content.privacy.droits_req_2') }}</li>
                        <li>{{ __('legal_content.privacy.droits_req_3') }}</li>
                    </ul>
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-slate-700 mt-4">
                        <p class="font-semibold">{{ __('legal_content.privacy.delai_title') }}</p>
                        <p>{{ __('legal_content.privacy.delai_p') }}</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.mineurs_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.mineurs_p') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.cookies_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.cookies_p') }}</p>
                </section>
                
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.modifications_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.modifications_p') }}</p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('legal_content.privacy.contact_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('legal_content.privacy.contact_intro') }}</p>
                    <ul class="list-disc pl-6 text-slate-600 space-y-1">
                        <li>{{ __('legal_content.privacy.contact_email_label') }} : Upfiesta.proj@gmail.com</li>
                        <li>{{ __('legal_content.privacy.contact_phone_label') }} : +228 99 46 25 51</li>
                        <li>{{ __('legal_content.privacy.contact_site_label') }} : <span class="font-semibold">https://upfiesta.saeicubetech.com</span></li>
                        <li>{{ __('legal_content.privacy.contact_address_label') }} : Lomé, Togo</li>
                    </ul>
                </section>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} Up Fiesta. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
