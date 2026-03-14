<?php

namespace App\Filament\Resources\AssignedServiceResource\Pages;

use App\Filament\Resources\AssignedServiceResource;
use App\Notifications\ServiceAssignedNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateAssignedService extends CreateRecord
{
    protected static string $resource = AssignedServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = \Illuminate\Support\Facades\Auth::user()?->id ?? null;
        return $data;
    }

    protected function afterCreate(): void
    {
        // Notify the provider
        $provider = $this->record->provider->user;
        $provider->notify(new ServiceAssignedNotification($this->record));
    }
}
