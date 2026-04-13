<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer ou mettre à jour l'utilisateur admin (mot de passe défini via .env)
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@upfiesta.tg'],
            [
                'name' => 'Admin Upfiesta',
                'password' => \Illuminate\Support\Facades\Hash::make(env('ADMIN_INITIAL_PASSWORD', 'Upadmin@2620!')),
                'role' => 'admin',
            ]
        );

        // Créer ou mettre à jour le pays Togo
        $togo = \App\Models\Country::updateOrCreate(
            ['code' => 'TG'],
            [
                'name' => 'Togo',
                'phone_code' => '+228',
                'currency' => 'XOF',
            ]
        );

        // Créer les villes principales (Limité à Lomé pour le moment)
        $villes = ['Lomé'];
        foreach ($villes as $ville) {
            \App\Models\City::updateOrCreate(
                [
                    'country_id' => $togo->id,
                    'name' => $ville,
                ],
                []
            );
        }

        // Créer les catégories de services (Prestations professionnelles uniquement)
        $categories = [
            'Traiteur' => 'traiteur',
            'Décoration' => 'decoration',
            'Photographie & Vidéo' => 'photographie-video',
            'Animation & DJ' => 'animation-dj',
            'Location de salle' => 'location-salle',
            'Sécurité' => 'securite',
            'Maquillage & Coiffure' => 'maquillage-coiffure',
            'Location de voiture' => 'location-voiture',
            'Hôtesse & Accueil' => 'hotesse-accueil',
        ];

        foreach ($categories as $name => $slug) {
            \App\Models\ServiceCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'kind' => \App\Models\ServiceCategory::KIND_PRESTATIONS,
                ]
            );
        }
    }
}



