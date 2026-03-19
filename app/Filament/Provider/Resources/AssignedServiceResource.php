<?php

namespace App\Filament\Provider\Resources;

use App\Models\AssignedService;
use App\Filament\Provider\Resources\AssignedServiceResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AssignedServiceResource extends Resource
{
    protected static ?string $model = AssignedService::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Mes Assignations';

    protected static ?string $modelLabel = 'Assignation';

    protected static ?string $pluralModelLabel = 'Assignations';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du Service')
                    ->schema([
                        Forms\Components\TextInput::make('serviceRequest.subject')
                            ->label('Service')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('serviceRequest.user.name')
                            ->label('Client')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('serviceRequest.budget')
                            ->label('Budget')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => number_format($state, 0) . ' XOF'),

                        Forms\Components\Textarea::make('serviceRequest.description')
                            ->label('Description')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('Votre Réponse')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'accepted' => 'Accepté',
                                'rejected' => 'Rejeté',
                                'completed' => 'Complété',
                            ])
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du refus')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record?->status === 'rejected'),

                        Forms\Components\TextInput::make('responded_at')
                            ->label('Répondé le')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i')),

                        Forms\Components\TextInput::make('completed_at')
                            ->label('Complété le')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i'))
                            ->visible(fn ($record) => $record?->status === 'completed'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serviceRequest.subject')
                    ->label('Service')
                    ->icon('heroicon-m-briefcase')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('serviceRequest.user.name')
                    ->label('Client')
                    ->icon('heroicon-m-user-circle')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('serviceRequest.budget')
                    ->label('Budget')
                    ->icon('heroicon-m-currency-dollar')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' XOF')
                    ->sortable()
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('serviceRequest.event_date')
                    ->label('Date requise')
                    ->icon('heroicon-m-calendar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Votre réponse')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                        'info' => 'completed',
                    ])
                    ->icons([
                        'pending' => 'heroicon-m-clock',
                        'accepted' => 'heroicon-m-check-circle',
                        'rejected' => 'heroicon-m-x-circle',
                        'completed' => 'heroicon-m-check',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'En attente',
                        'accepted' => 'Accepté',
                        'rejected' => 'Rejeté',
                        'completed' => 'Complété',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Assigné le')
                    ->icon('heroicon-m-arrow-path')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'accepted' => 'Accepté',
                        'rejected' => 'Rejeté',
                        'completed' => 'Complété',
                    ])
                    ->indicator('Réponse'),

                Tables\Filters\Filter::make('event_date')
                    ->form([
                        Forms\Components\DatePicker::make('event_from')
                            ->label('Du'),
                        Forms\Components\DatePicker::make('event_until')
                            ->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['event_from'],
                                fn (Builder $q, $date) => $q->whereDate('service_requests.event_date', '>=', $date)
                            )
                            ->when(
                                $data['event_until'],
                                fn (Builder $q, $date) => $q->whereDate('service_requests.event_date', '<=', $date)
                            );
                    })
                    ->indicator('Période'),
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
                    ->modalHeading('Accepter cette assignation?')
                    ->modalDescription('Vous vous engagez à réaliser ce service.')
                    ->modalSubmitActionLabel('Oui, accepter')
                    ->visible(fn (AssignedService $record) => $record->isPending())
                    ->action(fn (AssignedService $record) => static::acceptAssignment($record)),

                Tables\Actions\Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (AssignedService $record) => $record->isPending())
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du refus')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('Décrivez pourquoi vous ne pouvez pas accepter ce service...'),
                    ])
                    ->action(fn (AssignedService $record, array $data) => static::rejectAssignment($record, $data['rejection_reason'])),

                Tables\Actions\Action::make('complete')
                    ->label('Marquer complété')
                    ->icon('heroicon-m-check')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Marquer le service comme complété?')
                    ->modalDescription('Cette action ne peut pas être annulée.')
                    ->modalSubmitActionLabel('Oui, complété')
                    ->visible(fn (AssignedService $record) => $record->isAccepted())
                    ->action(fn (AssignedService $record) => static::completeAssignment($record)),
            ])
            ->defaultSort('assigned_at', 'desc');
    }

    public static function acceptAssignment(AssignedService $record): void
    {
        if (!$record->isPending()) {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Cette assignation ne peut pas être acceptée.')
                ->danger()
                ->send();
            return;
        }

        $record->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        $record->serviceRequest->update(['status' => 'assigned']);

        \Filament\Notifications\Notification::make()
            ->title('Assignation acceptée')
            ->body('Vous avez accepté cette assignation.')
            ->success()
            ->send();
    }

    public static function rejectAssignment(AssignedService $record, string $reason): void
    {
        if (!$record->isPending()) {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Cette assignation ne peut pas être rejetée.')
                ->danger()
                ->send();
            return;
        }

        $record->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'responded_at' => now(),
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Assignation rejetée')
            ->body('Vous avez rejeté cette assignation.')
            ->warning()
            ->send();
    }

    public static function completeAssignment(AssignedService $record): void
    {
        if (!$record->isAccepted()) {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Cette assignation ne peut pas être complétée.')
                ->danger()
                ->send();
            return;
        }

        $record->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $record->serviceRequest->update(['status' => 'completed']);

        \Filament\Notifications\Notification::make()
            ->title('Service complété')
            ->body('Vous avez marqué ce service comme complété.')
            ->success()
            ->send();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $provider = \App\Models\Provider::where('user_id', $user->id)->first();

        return parent::getEloquentQuery()
            ->where('provider_id', $provider?->id)
            ->with(['serviceRequest.user', 'admin']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignedServices::route('/'),
            'view' => Pages\ViewAssignedService::route('/{record}'),
        ];
    }
}
