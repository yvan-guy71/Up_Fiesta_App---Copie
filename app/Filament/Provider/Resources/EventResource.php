<?php

namespace App\Filament\Provider\Resources;

use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Notifications\EventParticipationStatusNotification;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Mes Missions / Événements';

    protected static ?string $modelLabel = 'Événement';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de la Mission')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Nom de l\'événement')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('date')
                            ->label('Date et Heure')
                            ->disabled(),
                        Forms\Components\Placeholder::make('city_name')
                            ->label('Ville')
                            ->content(fn (Event $record): string => $record->city?->name ?? 'Non spécifiée'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description du projet')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Événement')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ville')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pivot_status')
                    ->label('Mon Statut')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmé',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                        default => $state ?? 'En attente',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('discussion')
                    ->label('Discussion')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->url(fn (Event $record) => route('messages.show', $record->user_id)),
                Tables\Actions\Action::make('accepter')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Event $record): bool => ($record->pivot_status ?? 'pending') === 'pending')
                    ->action(function (Event $record) {
                        $provider = Auth::user()?->provider;
                        
                        if (!$provider) return;
                        
                        $record->providers()->updateExistingPivot($provider->id, [
                            'status' => 'confirmed'
                        ]);

                        // Notifier le créateur de l'événement
                        if ($record->user) {
                            $record->user->notify(new EventParticipationStatusNotification($record, $provider, 'confirmed'));
                        }

                        // Notifier tous les administrateurs
                        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $admins */
                        $admins = \App\Models\User::where('role', 'admin')->get();
                        foreach ($admins as $admin) {
                            $admin->notify(new EventParticipationStatusNotification($record, $provider, 'confirmed'));
                        }

                        FilamentNotification::make()
                            ->title('Mission acceptée')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('refuser')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Event $record): bool => ($record->pivot_status ?? 'pending') === 'pending')
                    ->action(function (Event $record) {
                        $provider = Auth::user()?->provider;
                        
                        if (!$provider) return;
                        
                        $record->providers()->updateExistingPivot($provider->id, [
                            'status' => 'cancelled'
                        ]);

                        // Notifier le créateur de l'événement
                        if ($record->user) {
                            $record->user->notify(new EventParticipationStatusNotification($record, $provider, 'cancelled'));
                        }

                        // Notifier tous les administrateurs
                        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $admins */
                        $admins = \App\Models\User::where('role', 'admin')->get();
                        foreach ($admins as $admin) {
                            $admin->notify(new EventParticipationStatusNotification($record, $provider, 'cancelled'));
                        }

                        FilamentNotification::make()
                            ->title('Mission refusée')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $provider = Auth::user()?->provider;

        if (!$provider) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->with(['city'])
            ->select('events.*')
            ->join('event_provider', 'events.id', '=', 'event_provider.event_id')
            ->where('event_provider.provider_id', $provider->id)
            ->selectRaw('event_provider.status as pivot_status');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Provider\Resources\EventResource\Pages\ManageEvents::route('/'),
        ];
    }
}
