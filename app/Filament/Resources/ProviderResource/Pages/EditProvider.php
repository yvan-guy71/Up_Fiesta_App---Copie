<?php

namespace App\Filament\Resources\ProviderResource\Pages;

use App\Filament\Resources\ProviderResource;
use App\Notifications\ProviderApprovedNotification;
use App\Notifications\ProviderRejectedNotification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Notification;

class EditProvider extends EditRecord
{
    protected static string $resource = ProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Track status change
        if ($this->record->verification_status !== ($data['verification_status'] ?? null)) {
            // Add timestamp and admin id on status change
            $data['verified_at'] = now();
            $userId = auth()->user()?->id;
            if ($userId) {
                $data['verified_by'] = $userId;
            }
            
            // Sync is_verified with verification_status
            $data['is_verified'] = ($data['verification_status'] ?? null) === 'approved';
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $originalStatus = $this->record->getOriginal('verification_status');
        $newStatus = $this->record->verification_status;

        // Send notification if status changed
        if ($originalStatus !== $newStatus) {
            // Ensure user is loaded
            if (!$this->record->relationLoaded('user')) {
                $this->record->load('user');
            }
            
            if (!$this->record->user) {
                return;
            }
            
            if ($newStatus === 'approved') {
                Notification::send($this->record->user, new ProviderApprovedNotification());
            } elseif ($newStatus === 'rejected') {
                Notification::send(
                    $this->record->user,
                    new ProviderRejectedNotification($this->record, $this->record->rejection_reason ?? '')
                );
            }
        }
    }
}
