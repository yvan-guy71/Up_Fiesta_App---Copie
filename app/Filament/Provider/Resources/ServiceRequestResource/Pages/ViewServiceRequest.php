<?php

namespace App\Filament\Provider\Resources\ServiceRequestResource\Pages;

use App\Filament\Provider\Resources\ServiceRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations du Client')
                    ->icon('heroicon-m-user-circle')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Nom du client'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('user.phone')
                            ->label('Téléphone')
                            ->icon('heroicon-m-phone')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('location')
                            ->label('Localisation du service')
                            ->icon('heroicon-m-map-pin'),
                    ]),

                Infolists\Components\Section::make('Description du Service')
                    ->icon('heroicon-m-briefcase')
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('subject')
                            ->label('Sujet du service')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description complète')
                            ->prose(),
                        Infolists\Components\TextEntry::make('kind')
                            ->label('Type de service')
                            ->badge()
                            ->color('info'),
                    ]),

                Infolists\Components\Section::make('Détails du Service')
                    ->icon('heroicon-m-calendar')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('event_date')
                            ->label('Date et heure souhaitées')
                            ->icon('heroicon-m-calendar')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('budget')
                            ->label('Budget proposé par le client')
                            ->icon('heroicon-m-currency-dollar')
                            ->formatStateUsing(fn ($state) => number_format($state, 0) . ' XOF')
                            ->color('success')
                            ->size('lg'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processed' => 'info',
                                'assigned' => 'success',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'En attente',
                                'processed' => 'Traitée',
                                'assigned' => 'Assignée',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Demande reçue le')
                            ->icon('heroicon-m-calendar')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
