<?php

namespace App\Filament\Provider\Resources\ProviderResource\Pages;

use App\Filament\Provider\Resources\ProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProviders extends ManageRecords
{
    protected static string $resource = ProviderResource::class;

    protected function getHeaderActions(): array
    {
        // On ne permet pas de créer un profil manuellement ici s'il existe déjà
        // On pourrait ajouter une logique pour n'afficher le bouton que si l'utilisateur n'a pas de profil
        return [
            Actions\CreateAction::make()
                ->label('Créer mon profil')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    return $data;
                })
                ->hidden(fn () => auth()->user()->provider()->exists()),
        ];
    }
}
