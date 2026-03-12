<?php

namespace App\Filament\Provider\Resources\EventResource\Pages;

use App\Filament\Provider\Resources\EventResource;
use Filament\Resources\Pages\ManageRecords;

class ManageEvents extends ManageRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
