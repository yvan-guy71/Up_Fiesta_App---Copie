<?php

namespace App\Filament\Provider\Resources;

use App\Filament\Provider\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Mes Réservations';

    protected static ?string $modelLabel = 'Réservation';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Prix Total')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_provider_response' => 'Réponse du prestataire en attente',
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'completed' => 'Terminée',
                        'rejected' => 'Refusée',
                        'cancelled' => 'Annulée',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending_provider_response' => 'warning',
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'rejected' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('event_details')
                    ->label('Détails de l\'événement')
                    ->limit(30)
                    ->tooltip(fn (Booking $record): string => $record->event_details ?? ''),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_provider_response' => 'En attente de réponse',
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'completed' => 'Terminée',
                        'rejected' => 'Refusée',
                        'cancelled' => 'Annulée',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('accept')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Accepter la réservation')
                    ->modalDescription('Êtes-vous sûr de vouloir accepter cette réservation ?')
                    ->visible(fn (Booking $record): bool => $record->status === 'pending_provider_response')
                    ->action(function (Booking $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'provider_response_at' => now(),
                        ]);
                        $client = $record->user;
                        if ($client) {
                            $client->notify(new \App\Notifications\ClientBookingAcceptedNotification($record));
                        }
                    })
                    ->successNotificationTitle('Réservation acceptée'),
                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du refus (facultatif)')
                            ->placeholder('Expliquez brièvement pourquoi vous refusez cette réservation...')
                            ->maxLength(500)
                            ->rows(4),
                    ])
                    ->visible(fn (Booking $record): bool => $record->status === 'pending_provider_response')
                    ->action(function (Booking $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'] ?? null,
                            'provider_response_at' => now(),
                        ]);
                        $client = $record->user;
                        if ($client) {
                            $client->notify(new \App\Notifications\ClientBookingRejectedNotification($record));
                        }
                    })
                    ->successNotificationTitle('Réservation refusée'),
                Tables\Actions\Action::make('mark_done')
                    ->label('Marquer comme fait')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->status === 'confirmed' && ! $record->provider_done)
                    ->action(function (Booking $record) {
                        $record->update([
                            'provider_done' => true,
                            'provider_done_at' => now(),
                        ]);
                        $admins = \App\Models\User::where('role', 'admin')->get();
                        if ($admins->isNotEmpty()) {
                            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\BookingMarkedDoneNotification($record));
                        }
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => in_array($record->status, ['pending', 'confirmed']))
                    ->action(fn (Booking $record) => $record->update(['status' => 'cancelled'])),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $provider = Auth::user()?->provider;

        if (!$provider) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()->where('provider_id', $provider->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBookings::route('/'),
        ];
    }
}
