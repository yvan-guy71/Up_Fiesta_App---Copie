<?php

namespace App\Filament\Resources\AssignedServiceResource\Pages;

use App\Filament\Resources\AssignedServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssignedServices extends ListRecords
{
    protected static string $resource = AssignedServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Assigner un service'),
        ];
    }
}
