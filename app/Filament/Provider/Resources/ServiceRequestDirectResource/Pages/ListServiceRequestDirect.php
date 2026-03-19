<?php

namespace App\Filament\Provider\Resources\ServiceRequestDirectResource\Pages;

use App\Filament\Provider\Resources\ServiceRequestDirectResource;
use Filament\Resources\Pages\ListRecords;

class ListServiceRequestDirect extends ListRecords
{
    protected static string $resource = ServiceRequestDirectResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
