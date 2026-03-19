<?php

namespace App\Filament\Provider\Resources;

use App\Filament\Provider\Resources\ServiceRequestDirectResource\Pages;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ServiceRequestDirectResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Demandes Directes';

    protected static ?string $modelLabel = 'Demande Directe';

    protected static ?string $pluralModelLabel = 'Demandes Directes';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->icon('heroicon-m-user-circle')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Service demandé')
                    ->icon('heroicon-m-briefcase')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'assigned',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'pending' => 'heroicon-m-clock',
                        'assigned' => 'heroicon-m-check-circle',
                        'cancelled' => 'heroicon-m-x-circle',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'assigned' => 'Acceptée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('budget')
                    ->label('Budget')
                    ->icon('heroicon-m-currency-dollar')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' XOF')
                    ->sortable()
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Date souhaitée')
                    ->icon('heroicon-m-calendar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'assigned' => 'Acceptée',
                        'cancelled' => 'Annulée',
                    ])
                    ->indicator('Statut'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->label('Voir'),

                Tables\Actions\Action::make('accept')
                    ->label('Accepter')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Accepter cette demande?')
                    ->modalDescription('Vous vous engagez à réaliser ce service.')
                    ->modalSubmitActionLabel('Oui, accepter')
                    ->visible(fn (ServiceRequest $record) => $record->status === 'pending')
                    ->action(fn (ServiceRequest $record) => static::acceptRequest($record)),

                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (ServiceRequest $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du refus')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('Décrivez pourquoi vous ne pouvez pas accepter cette demande...'),
                    ])
                    ->action(fn (ServiceRequest $record, array $data) => static::rejectRequest($record, $data['rejection_reason'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $provider = Auth::user()?->provider;

        if (!$provider) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Show only requests directly selected to this provider
        return parent::getEloquentQuery()
            ->where('provider_id', $provider->id)
            ->with(['user', 'provider']);
    }

    public static function acceptRequest(ServiceRequest $record): void
    {
        if ($record->status !== 'pending') {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Cette demande ne peut pas être acceptée.')
                ->danger()
                ->send();
            return;
        }

        // Update status to assigned
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
    }

    public static function rejectRequest(ServiceRequest $record, string $reason): void
    {
        if ($record->status !== 'pending') {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Cette demande ne peut pas être rejetée.')
                ->danger()
                ->send();
            return;
        }

        // Clear provider_id and keep status as pending
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
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequestDirect::route('/'),
            'view' => Pages\ViewServiceRequestDirect::route('/{record}'),
        ];
    }
}
