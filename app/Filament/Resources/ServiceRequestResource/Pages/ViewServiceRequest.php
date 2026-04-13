<?php

namespace App\Filament\Resources\ServiceRequestResource\Pages;

use App\Filament\Resources\ServiceRequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (is_null($this->getRecord()->viewed_at)) {
            $this->getRecord()->update(['viewed_at' => now()]);
        }
    }
}
