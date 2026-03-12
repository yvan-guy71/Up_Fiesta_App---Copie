<?php

namespace App\Filament\Provider\Resources\BookingResource\Pages;

use App\Filament\Provider\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBookings extends ManageRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // On ne permet pas au prestataire de créer une réservation manuellement ici
            // car elles viennent généralement du front-end client.
        ];
    }
}
