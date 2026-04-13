{{-- Footer Component Professionnel --}}
@props([
    'bgColor' => 'bg-secondary-900',
])

<footer {{ $attributes->merge(['class' => "$bgColor text-white border-t border-secondary-800 transition-all duration-300"]) }}>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Colonne 1: Brand -->
            <div class="space-y-4">
                <h3 class="text-xl font-bold text-primary-400">Up Fiesta</h3>
                <p class="text-secondary-300 text-sm leading-relaxed">
                    {{ $brandDescription ?? 'Connectant les prestataires de services exceptionnels avec des clients qui en ont besoin.' }}
                </p>
                <div class="flex gap-3 text-secondary-400">
                    {{ $socialLinks ?? '' }}
                </div>
            </div>

            <!-- Colonne 2: Produit -->
            <div>
                <h4 class="font-semibold mb-4 text-white">Produit</h4>
                <ul class="space-y-2 text-sm text-secondary-300">
                    {{ $productLinks ?? '' }}
                </ul>
            </div>

            <!-- Colonne 3: Entreprise -->
            <div>
                <h4 class="font-semibold mb-4 text-white">Entreprise</h4>
                <ul class="space-y-2 text-sm text-secondary-300">
                    {{ $companyLinks ?? '' }}
                </ul>
            </div>

            <!-- Colonne 4: Support -->
            <div>
                <h4 class="font-semibold mb-4 text-white">Support</h4>
                <ul class="space-y-2 text-sm text-secondary-300">
                    {{ $supportLinks ?? '' }}
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-secondary-800 my-8"></div>

        <!-- Bottom -->
        <div class="flex flex-col md:flex-row justify-between items-center text-sm text-secondary-400">
            <p>&copy; {{ date('Y') }} Up Fiesta. All rights reserved.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                {{ $legalLinks ?? '' }}
            </div>
        </div>
    </div>
</footer>
