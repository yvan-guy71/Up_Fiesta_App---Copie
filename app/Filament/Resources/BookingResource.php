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
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')->label('Paiement')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success', 'pending' => 'warning', 'failed' => 'danger', default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('provider_done')->label('Prestataire')
                    ->boolean()
                    ->tooltip(fn (Booking $r) => $r->provider_done ? 'A indiqué terminé' : 'En cours'),
                Tables\Columns\IconColumn::make('require_client_review')->label('Notation demandée')
                    ->boolean(),
                Tables\Columns\IconColumn::make('review.id')->label('Noté')
                    ->boolean()
                    ->state(fn (Booking $r) => $r->review()->exists())
                    ->tooltip(fn (Booking $r) => $r->review?->comment),
                Tables\Columns\TextColumn::make('review.rating')->label('Note')
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 2 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state ? $state . ' ★' : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin_verification_status')->label('Vérification')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'verified' => 'success', 'pending' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payout_status')->label('Finalisation Admin')->badge()
                    ->color(fn (string $state) => match ($state) { 'completed' => 'success', 'pending' => 'warning', default => 'gray' }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'En attente', 'confirmed' => 'Confirmé', 'completed' => 'Terminé', 'cancelled' => 'Annulé',
                ]),
                Tables\Filters\SelectFilter::make('payment_status')->label('Paiement')->options([
                    'unpaid' => 'Non payé', 'pending' => 'En attente', 'paid' => 'Payé',
                ]),
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label('Prestataire')
                    ->relationship('provider', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Ville')
                    ->relationship('provider.city', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('admin_verification_status')
                    ->label('Vérification')
                    ->options([
                        'pending' => 'En attente',
                        'verified' => 'Vérifié',
                    ]),
                Tables\Filters\Filter::make('event_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Du'),
                        \Filament\Forms\Components\DatePicker::make('to')->label('Au'),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('event_date', '>=', $date))
                            ->when($data['to'], fn ($q, $date) => $q->whereDate('event_date', '<=', $date));
                    }),
                Tables\Filters\Filter::make('total_price')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min')->label('Prix min')->numeric(),
                        \Filament\Forms\Components\TextInput::make('max')->label('Prix max')->numeric(),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['min'], fn ($q, $min) => $q->where('total_price', '>=', $min))
                            ->when($data['max'], fn ($q, $max) => $q->where('total_price', '<=', $max));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $r) => $r->status === 'pending')
                    ->action(function (Booking $r) {
                        $r->update(['status' => 'confirmed']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Réservation confirmée')
                            ->body('La réservation est confirmée.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('verify_by_admin')
                    ->label('Vérifier la qualité')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Vérifier la qualité du service')
                    ->modalDescription('Cette action valide la qualité du service rendu pour nos statistiques et la sélection des meilleurs prestataires.')
                    ->visible(fn (Booking $r) => $r->provider_done && $r->admin_verification_status === 'pending')
                    ->action(function (Booking $r) {
                        $reviewService = app(\App\Services\BookingReviewService::class);
                        $reviewService->verifyBookingByAdmin($r, auth()->user()?->id);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Travail vérifié')
                            ->body('Le travail du prestataire a été vérifié pour contrôle qualité.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('complete_admin')
                    ->label('Marquer comme terminé')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $r) => $r->status === 'confirmed' && $r->provider_done && $r->admin_verification_status === 'verified' && $r->status !== 'completed')
                    ->action(function (Booking $r) {
                        $r->update(['status' => 'completed']);
                        \Filament\Notifications\Notification::make()
                            ->title('Statut mis à jour')
                            ->body('La réservation est maintenant marquée comme terminée.')
                            ->success()
                            ->send();
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
