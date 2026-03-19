<?php

namespace App\Filament\Provider\Resources\ServiceRequestDirectResource\Pages;

use App\Filament\Provider\Resources\ServiceRequestDirectResource;
use App\Models\ServiceRequest;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Actions;
use Filament\Forms;

class ViewServiceRequestDirect extends ViewRecord
{
    protected static string $resource = ServiceRequestDirectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('accept')
                ->label('✓ Accepter')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading('Accepter cette demande?')
                ->modalDescription('Vous vous engagez à réaliser ce service dans les délais impartis.')
                ->modalSubmitActionLabel('Oui, accepter')
                ->visible(fn (?ServiceRequest $record) => $record?->status === 'pending')
                ->action(fn (ServiceRequest $record) => $this->acceptRequest($record)),

            Actions\Action::make('reject')
                ->label('✗ Refuser')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->size('lg')
                ->visible(fn (?ServiceRequest $record) => $record?->status === 'pending')
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label('Raison du refus')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('Décrivez pourquoi vous ne pouvez pas accepter cette demande (ex: trop occupé, compétences insuffisantes, etc.)'),
                ])
                ->action(fn (ServiceRequest $record, array $data) => $this->rejectRequest($record, $data['rejection_reason'])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Service demandé')
                    ->icon('heroicon-m-briefcase')
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('subject')
                            ->label('Titre du service')
                            ->size('lg'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description complète')
                            ->prose(),
                        Infolists\Components\TextEntry::make('kind')
                            ->label('Type de service')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'prestations' => 'Prestations',
                                'domestiques' => 'Domestiques',
                                default => $state,
                            }),
                    ]),

                Infolists\Components\Section::make('Informations du client')
                    ->icon('heroicon-m-user-circle')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Nom'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->copyMessage('Email copié'),
                        Infolists\Components\TextEntry::make('user.phone')
                            ->label('Téléphone')
                            ->icon('heroicon-m-phone')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('location')
                            ->label('Localisation du service')
                            ->icon('heroicon-m-map-pin'),
                    ]),

                Infolists\Components\Section::make('Détails du service')
                    ->icon('heroicon-m-calendar')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('event_date')
                            ->label('Date & Heure souhaitée')
                            ->icon('heroicon-m-calendar')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('budget')
                            ->label('Budget proposé')
                            ->icon('heroicon-m-currency-dollar')
                            ->formatStateUsing(fn ($state) => number_format($state, 0) . ' XOF')
                            ->color('success')
                            ->size('lg'),
                    ]),

                Infolists\Components\Section::make('Votre réponse')
                    ->icon('heroicon-m-check-circle')
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'assigned' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match($state) {
                                'pending' => '⏳ En attente',
                                'assigned' => '✓ Acceptée',
                                'cancelled' => '✗ Annulée',
                                default => $state,
                            })
                            ->size('lg'),
                    ]),

                Infolists\Components\Section::make('Dates importantes')
                    ->icon('heroicon-m-calendar')
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Demande reçue le')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->collapsed(),
            ]);
    }

    protected function acceptRequest(ServiceRequest $record): void
    {
        if ($record->status !== 'pending') {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Cette demande ne peut pas être acceptée.')
                ->danger()
                ->send();
            return;
        }

        $record->update([
            'status' => 'assigned',
        ]);

        // Notify client
        $client = $record->user;
        $client->notify(new \App\Notifications\ServiceRequestAcceptedByProviderNotification($record));

        \Filament\Notifications\Notification::make()
            ->title('✓ Demande acceptée')
            ->body('Vous avez accepté cette demande. Le client en a été notifié.')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function rejectRequest(ServiceRequest $record, string $reason): void
    {
        if ($record->status !== 'pending') {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Cette demande ne peut pas être rejetée.')
                ->danger()
                ->send();
            return;
        }

        $record->update([
            'provider_id' => null,
        ]);

        // Notify client with rejection reason
        $client = $record->user;
        $client->notify(new \App\Notifications\ServiceRequestRejectedByProviderNotification($record, $reason));

        \Filament\Notifications\Notification::make()
            ->title('✗ Demande rejetée')
            ->body('Vous avez rejeté cette demande. Le client a été notifié et peut choisir un autre prestataire.')
            ->warning()
            ->send();

        $this->redirect(static::getResource()::getUrl('index'));
    }
}
