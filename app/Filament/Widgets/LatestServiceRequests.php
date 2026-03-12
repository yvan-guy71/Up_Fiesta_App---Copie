<?php

namespace App\Filament\Widgets;

use App\Models\ServiceRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestServiceRequests extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières demandes de services des clients';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceRequest::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'processed' => 'Traitée',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    })
                    ->label('Statut'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Reçue le')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir détails')
                    ->url(fn (ServiceRequest $record): string => "/admin/service-requests/{$record->id}/edit")
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
