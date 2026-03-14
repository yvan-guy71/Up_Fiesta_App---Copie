<?php

namespace App\Filament\Provider\Resources\AssignedServiceResource\Pages;

use App\Filament\Provider\Resources\AssignedServiceResource;
use App\Models\AssignedService;
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
                Infolists\Components\Section::make('Service demandé')
                    ->icon('heroicon-m-briefcase')
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('serviceRequest.subject')
                            ->label('Titre du service')
                            ->size('lg'),
                        Infolists\Components\TextEntry::make('serviceRequest.description')
                            ->label('Description complète')
                            ->prose(),
                        Infolists\Components\TextEntry::make('serviceRequest.kind')
                            ->label('Type de service')
                            ->badge()
                            ->color('info'),
                    ]),

                Infolists\Components\Section::make('Informations du client')
                    ->icon('heroicon-m-user-circle')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('serviceRequest.user.name')
                            ->label('Nom'),
                        Infolists\Components\TextEntry::make('serviceRequest.user.email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->copyMessage('Email copié'),
                        Infolists\Components\TextEntry::make('serviceRequest.user.phone')
                            ->label('Téléphone')
                            ->icon('heroicon-m-phone')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('serviceRequest.location')
                            ->label('Localisation du service')
                            ->icon('heroicon-m-map-pin'),
                    ]),

                Infolists\Components\Section::make('Détails du service')
                    ->icon('heroicon-m-calendar')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('serviceRequest.event_date')
                            ->label('Date & Heure')
                            ->icon('heroicon-m-calendar')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('serviceRequest.budget')
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
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'completed' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match($state) {
                                'pending' => '⏳ En attente',
                                'accepted' => '✓ Accepté',
                                'rejected' => '✗ Rejeté',
                                'completed' => '✓ Complété',
                                default => $state,
                            })
                            ->size('lg'),
                        
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Raison du refus')
                            ->visible(fn (?AssignedService $record) => $record?->status === 'rejected')
                            ->prose()
                            ->color('danger'),
                        
                        Infolists\Components\TextEntry::make('responded_at')
                            ->label('Vous avez répondu le')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn (?AssignedService $record) => $record?->responded_at !== null)
                            ->icon('heroicon-m-check'),
                        
                        Infolists\Components\TextEntry::make('completed_at')
                            ->label('Service complété le')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn (?AssignedService $record) => $record?->status === 'completed')
                            ->icon('heroicon-m-check-circle'),
                    ]),

                Infolists\Components\Section::make('Dates importantes')
                    ->icon('heroicon-m-calendar')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('assigned_at')
                            ->label('Assigné le')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->collapsed(),
            ]);
    }
}

