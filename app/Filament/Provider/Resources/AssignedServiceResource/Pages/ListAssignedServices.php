<?php

namespace App\Filament\Provider\Resources\AssignedServiceResource\Pages;

use App\Filament\Provider\Resources\AssignedServiceResource;
use Filament\Resources\Pages\ListRecords;

class ListAssignedServices extends ListRecords
{
    protected static string $resource = AssignedServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for provider - only admin can assign
        ];
    }
}
