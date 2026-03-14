<?php

namespace App\Filament\Widgets;

use App\Models\AssignedService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAssignments extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AssignedService::query()
                    ->with(['serviceRequest', 'provider.user'])
                    ->latest('assigned_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('serviceRequest.subject')
                    ->label('Service')
                    ->limit(30)
                    ->searchable()
                    ->icon('heroicon-m-briefcase'),

                Tables\Columns\TextColumn::make('provider.user.name')
                    ->label('Prestataire')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('serviceRequest.user.name')
                    ->label('Client')
                    ->icon('heroicon-m-user-circle'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                        'info' => 'completed',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'En attente',
                        'accepted' => 'Accepté',
                        'rejected' => 'Rejeté',
                        'completed' => 'Complété',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Assigné')
                    ->dateTime('d/m/Y H:i')
                    ->icon('heroicon-m-calendar'),
            ])
            ->defaultSort('assigned_at', 'desc')
            ->paginated(false);
    }
}
