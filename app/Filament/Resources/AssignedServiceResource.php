<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignedServiceResource\Pages;
use App\Models\AssignedService;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignedServiceResource extends Resource
{
    protected static ?string $model = AssignedService::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?string $navigationLabel = 'Assignations';
    
    protected static ?string $modelLabel = 'Assignation';
    
    protected static ?string $pluralModelLabel = 'Assignations';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Assignation du service')
                    ->description('Sélectionnez un service demandé et un prestataire à assigner')
                    ->schema([
                        Forms\Components\Select::make('service_request_id')
                            ->label('Service demandé')
                            ->relationship(
                                'serviceRequest',
                                'subject',
                                fn (Builder $query) => $query->latest()
                                    ->whereDoesntHave('assignedServices', function ($q) {
                                        $q->where('status', 'accepted');
                                    })
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('provider_id', null))
                            ->getOptionLabelFromRecordUsing(fn (ServiceRequest $record) => "{$record->subject} [{$record->kind}] - {$record->event_date->format('d/m/Y')} - Budget: " . number_format($record->budget, 0) . " XOF")
                            ->createOptionForm([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name', fn ($query) => $query->where('role', 'client'))
                                    ->required()
                                    ->label('Client'),
                                Forms\Components\Select::make('type')
                                    ->options(['service' => 'Service', 'event' => 'Événement'])
                                    ->required()
                                    ->default('service'),
                                Forms\Components\Select::make('kind')
                                    ->options(['prestations' => 'Prestations (Événementiel)'])
                                    ->required()
                                    ->default('prestations')
                                    ->disabled(),
                                Forms\Components\TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('event_date')
                                    ->required(),
                                Forms\Components\TextInput::make('location')
                                    ->required(),
                                Forms\Components\TextInput::make('budget')
                                    ->numeric()
                                    ->prefix('XOF')
                                    ->required(),
                            ])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('min_experience')
                            ->label('Expérience min. (ans)')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('provider_id', null))
                            ->suffix('ans d\'exp.')
                            ->columnSpan(1),
                        
                        Forms\Components\Select::make('provider_id')
                            ->label('Prestataire à assigner')
                            ->relationship(
                                'provider',
                                'name',
                                function (Builder $query, Forms\Get $get) {
                                    $requestId = $get('service_request_id');
                                    $minExp = $get('min_experience') ?? 0;

                                    if (!$requestId) {
                                        return $query->whereRaw('1 = 0');
                                    }

                                    $request = ServiceRequest::find($requestId);
                                    if (!$request) {
                                        return $query->whereRaw('1 = 0');
                                    }

                                    // Filter providers by the 'kind' of the request
                                    $query->where(function($q) use ($request) {
                                        $q->whereHas('category', function ($sq) use ($request) {
                                            $sq->where('kind', $request->kind);
                                        })->orWhereHas('categories', function ($sq) use ($request) {
                                            $sq->where('kind', $request->kind);
                                        });
                                    });

                                    // Filter by years of experience
                                    if ($minExp > 0) {
                                        $query->where('years_of_experience', '>=', $minExp);
                                    }

                                    // Prioritize verified providers and more experienced ones
                                    return $query->orderByDesc('is_verified')
                                        ->orderByDesc('years_of_experience');
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull()
                            ->loadingMessage('Recherche des prestataires qualifiés...')
                            ->noSearchResultsMessage('Aucun prestataire correspondant trouvé avec ces critères.')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} (" . ($record->category?->name ?? 'Sans catégorie') . ") - Exp: {$record->years_of_experience} ans - " . ($record->is_verified ? '✅ Vérifié' : '⏳ Non vérifié')),
                    ])->columns(2),

                Forms\Components\Section::make('Statut')
                    ->description('Gérez le statut de l\'assignation')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'accepted' => 'Accepté',
                                'rejected' => 'Rejeté',
                                'completed' => 'Complété',
                            ])
                            ->required()
                            ->disabled(fn ($record) => $record !== null)
                            ->default('pending'),
                        
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du refus')
                            ->visible(fn ($get) => $get('status') === 'rejected')
                            ->maxLength(500)
                            ->placeholder('Décrivez la raison du refus...'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('serviceRequest.subject')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-briefcase')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('serviceRequest.user.name')
                    ->label('Client')
                    ->icon('heroicon-m-user-circle')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('provider.user.name')
                    ->label('Prestataire')
                    ->icon('heroicon-m-check-badge')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('serviceRequest.budget')
                    ->label('Budget')
                    ->icon('heroicon-m-currency-dollar')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' XOF')
                    ->sortable()
                    ->alignment('right')
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('serviceRequest.event_date')
                    ->label('Date du Service')
                    ->icon('heroicon-m-calendar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Assigné le')
                    ->icon('heroicon-m-arrow-path')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('responded_at')
                    ->label('Réponse le')
                    ->icon('heroicon-m-check')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('En attente')
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
                    ->indicator('Statut'),
                Tables\Filters\SelectFilter::make('serviceRequest.kind')
                    ->label('Type de service')
                    ->relationship('serviceRequest', 'kind')
                    ->indicator('Type'),
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label('Prestataire')
                    ->relationship('provider', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('serviceRequest.user_id')
                    ->label('Client')
                    ->relationship('serviceRequest.user', 'name')
                    ->searchable(),
                Tables\Filters\Filter::make('serviceRequest.budget')
                    ->form([
                        Forms\Components\TextInput::make('min')->label('Budget min')->numeric(),
                        Forms\Components\TextInput::make('max')->label('Budget max')->numeric(),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['min'], fn ($q, $min) => $q->whereHas('serviceRequest', fn ($sq) => $sq->where('budget', '>=', $min)))
                            ->when($data['max'], fn ($q, $max) => $q->whereHas('serviceRequest', fn ($sq) => $sq->where('budget', '<=', $max)));
                    }),
                Tables\Filters\Filter::make('serviceRequest.event_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Événement du'),
                        Forms\Components\DatePicker::make('to')->label('Événement jusqu\'au'),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereHas('serviceRequest', fn ($sq) => $sq->whereDate('event_date', '>=', $date)))
                            ->when($data['to'], fn ($q, $date) => $q->whereHas('serviceRequest', fn ($sq) => $sq->whereDate('event_date', '<=', $date)));
                    }),
                Tables\Filters\Filter::make('assigned_at')
                    ->form([
                        Forms\Components\DatePicker::make('assigned_from')->label('Assigné du'),
                        Forms\Components\DatePicker::make('assigned_until')->label('Assigné jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['assigned_from'],
                                fn (Builder $q, $date) => $q->whereDate('assigned_services.assigned_at', '>=', $date)
                            )
                            ->when(
                                $data['assigned_until'],
                                fn (Builder $q, $date) => $q->whereDate('assigned_services.assigned_at', '<=', $date)
                            );
                    })
                    ->indicator('Période'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->label('Voir'),
                
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil')
                    ->label('Modifier')
                    ->visible(fn (AssignedService $record) => $record->isPending()),
                
                Tables\Actions\Action::make('resend_notification')
                    ->label('Renvoyer notification')
                    ->icon('heroicon-m-bell-alert')
                    ->color('info')
                    ->visible(fn (AssignedService $record) => $record->isPending())
                    ->action(fn (AssignedService $record) => static::resendNotification($record)),
                
                Tables\Actions\Action::make('mark_completed')
                    ->label('Confirmer et notifier le client')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (AssignedService $record) => $record->isAccepted() || $record->isCompleted())
                    ->action(fn (AssignedService $record) => static::forceComplete($record)),
                
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-m-trash')
                    ->label('Supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer sélectionnées'),
                ]),
            ])
            ->defaultSort('assigned_at', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [];
    }

    public static function resendNotification(AssignedService $record): void
    {
        if (!$record->isPending()) {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Seules les assignations en attente peuvent être relancées.')
                ->warning()
                ->send();
            return;
        }

        $record->provider->user->notify(new \App\Notifications\ServiceAssignedNotification($record));

        \Filament\Notifications\Notification::make()
            ->title('Notification renvoyée')
            ->body('La notification a été renvoyée au prestataire.')
            ->success()
            ->send();
    }

    public static function forceComplete(AssignedService $record): void
    {
        if (!$record->isAccepted() && !$record->isCompleted()) {
            \Filament\Notifications\Notification::make()
                ->title('Action non valide')
                ->body('Seules les assignations acceptées ou complétées peuvent être confirmées.')
                ->danger()
                ->send();
            return;
        }

        // Only update status if not already completed
        if ($record->isPending() || $record->isAccepted()) {
            $record->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $record->serviceRequest->update(['status' => 'completed']);
        }

        // Always notify the client when the admin confirms completion
        $client = $record->serviceRequest->user;
        $client->notify(new \App\Notifications\AssignmentCompletionClientNotification($record));

        \Filament\Notifications\Notification::make()
            ->title('Service marqué complété')
            ->body('L\'assignation a été marquée comme complétée et le client a été notifié.')
            ->success()
            ->send();
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignedServices::route('/'),
            'create' => Pages\CreateAssignedService::route('/create'),
            'edit' => Pages\EditAssignedService::route('/{record}/edit'),
            'view' => Pages\ViewAssignedService::route('/{record}'),
        ];
    }
}
