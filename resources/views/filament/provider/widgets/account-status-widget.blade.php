<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-4">
            <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-xl">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-amber-600 dark:text-amber-400" />
            </div>
            
            <div class="flex-1">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    Compte en attente de validation
                </h2>
                <p class="text-slate-500 dark:text-slate-400">
                    Votre profil prestataire est en cours de vérification par nos administrateurs. 
                    Certaines fonctionnalités peuvent être limitées jusqu'à ce que votre compte soit approuvé.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                    <span class="w-2 h-2 mr-2 bg-amber-500 rounded-full animate-pulse"></span>
                    En attente
                </span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
