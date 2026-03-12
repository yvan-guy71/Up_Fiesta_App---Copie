<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Services\PayoutService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Réservations';
    protected static ?string $modelLabel = 'Réservation';
    protected static ?string $pluralModelLabel = 'Réservations';
    protected static ?string $navigationGroup = 'Transactions';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('provider.name')->label('Prestataire')->searchable(),
                Tables\Columns\TextColumn::make('event_date')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('XOF')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn (string $s) => match ($s) {
                        'pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')->label('Paiement')->badge()
                    ->color(fn (string $s) => match ($s) {
                        'paid' => 'success', 'pending' => 'warning', 'failed' => 'danger', default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('provider_done')->label('Prestataire')
                    ->boolean()
                    ->tooltip(fn (Booking $r) => $r->provider_done ? 'A indiqué terminé' : 'En cours'),
                Tables\Columns\TextColumn::make('platform_fee')->label('Frais')->money('XOF'),
                Tables\Columns\TextColumn::make('provider_amount')->label('À verser')->money('XOF'),
                Tables\Columns\TextColumn::make('payout_status')->label('Versement')->badge()
                    ->color(fn (string $s) => match ($s) { 'paid' => 'success', 'pending' => 'warning', default => 'gray' }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'En attente', 'confirmed' => 'Confirmé', 'completed' => 'Terminé', 'cancelled' => 'Annulé',
                ]),
                Tables\Filters\SelectFilter::make('payment_status')->label('Paiement')->options([
                    'unpaid' => 'Non payé', 'pending' => 'En attente', 'paid' => 'Payé',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $r) => $r->status === 'pending')
                    ->action(fn (Booking $r) => $r->update(['status' => 'confirmed'])),
                Tables\Actions\Action::make('complete_and_payout')
                    ->label('Terminer et payer le prestataire')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $r) => $r->status === 'confirmed' && $r->payment_status === 'paid' && $r->provider_done && $r->payout_status !== 'paid')
                    ->action(function (Booking $r) {
                        $r->update(['status' => 'completed']);
                        PayoutService::transfer($r);
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $r) => in_array($r->status, ['pending', 'confirmed']))
                    ->action(fn (Booking $r) => $r->update(['status' => 'cancelled'])),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageBookings::route('/')];
    }
}
