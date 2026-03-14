<?php

namespace App\Filament\Resources\AssignedServiceResource\Pages;

use App\Filament\Resources\AssignedServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssignedService extends EditRecord
{
    protected static string $resource = AssignedServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
