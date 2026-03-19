<?php

namespace App\Filament\Resources\AssignedServiceResource\Pages;

use App\Filament\Resources\AssignedServiceResource;
use App\Models\AssignedService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewAssignedService extends ViewRecord
{
    protected static string $resource = AssignedServiceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Service & Client')
                    ->icon('heroicon-m-briefcase')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('serviceRequest.subject')
                            ->label('Service'),
                        Infolists\Components\TextEntry::make('serviceRequest.user.name')
                            ->label('Nom du client'),
                        Infolists\Components\TextEntry::make('serviceRequest.user.email')
                            ->label('Email client')
                            ->icon('heroicon-m-envelope'),
                        Infolists\Components\TextEntry::make('serviceRequest.user.phone')
                            ->label('Téléphone')
                            ->icon('heroicon-m-phone'),
                    ]),

                Infolists\Components\Section::make('Détails du service')
                    ->icon('heroicon-m-document-text')
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('serviceRequest.description')
                            ->label('Description')
                            ->prose(),
                        Infolists\Components\TextEntry::make('serviceRequest.kind')
                            ->label('Type')
                            ->badge(),
                    ]),

                Infolists\Components\Section::make('Assignation')
                    ->icon('heroicon-m-arrow-path')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('provider.user.name')
                            ->label('Prestataire'),
                        Infolists\Components\TextEntry::make('provider.user.email')
                            ->label('Email prestataire')
                            ->icon('heroicon-m-envelope'),
                        Infolists\Components\TextEntry::make('admin.name')
                            ->label('Assigné par')
                            ->icon('heroicon-m-check-badge'),
                        Infolists\Components\TextEntry::make('assigned_at')
                            ->label('Date d\'assignation')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-m-calendar'),
                    ]),

                Infolists\Components\Section::make('Détails financiers')
                    ->icon('heroicon-m-currency-dollar')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('serviceRequest.budget')
                            ->label('Budget')
                            ->formatStateUsing(fn ($state) => number_format($state, 0) . ' XOF')
                            ->color('success'),
                        Infolists\Components\TextEntry::make('serviceRequest.location')
                            ->label('Localisation')
                            ->icon('heroicon-m-map-pin'),
                        Infolists\Components\TextEntry::make('serviceRequest.event_date')
                            ->label('Date du service')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-m-calendar'),
                    ]),

                Infolists\Components\Section::make('État de l\'assignation')
                    ->icon('heroicon-m-information-circle')
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Statut actuel')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'completed' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match($state) {
                                'pending' => 'En attente',
                                'accepted' => 'Accepté',
                                'rejected' => 'Rejeté',
                                'completed' => 'Complété',
                                default => $state,
                            }),
                        
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Raison du refus')
                            ->visible(fn (?AssignedService $record) => $record?->status === 'rejected')
                            ->prose(),
                        
                        Infolists\Components\TextEntry::make('responded_at')
                            ->label('Date de réponse')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn (?AssignedService $record) => $record?->responded_at !== null)
                            ->icon('heroicon-m-check'),
                        
                        Infolists\Components\TextEntry::make('completed_at')
                            ->label('Date de complétionition')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn (?AssignedService $record) => $record?->status === 'completed')
                            ->icon('heroicon-m-check-circle'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-m-pencil'),
            Actions\DeleteAction::make()
                ->icon('heroicon-m-trash'),
        ];
    }
}

