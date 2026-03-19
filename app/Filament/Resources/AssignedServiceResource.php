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
                                fn (Builder $query) => $query->whereDoesntHave('assignedServices', function ($q) {
                                    $q->where('status', 'accepted');
                                })
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('provider_id', null))
                            ->getOptionLabelFromRecordUsing(fn (ServiceRequest $record) => $record->subject . ' - Budget: ' . number_format($record->budget, 0) . ' XOF'),
                        
                        Forms\Components\Select::make('provider_id')
                            ->label('Prestataire à assigner')
                            ->relationship('provider', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
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
                
                Tables\Filters\Filter::make('assigned_at')
                    ->form([
                        Forms\Components\DatePicker::make('assigned_from')
                            ->label('Assigné du'),
                        Forms\Components\DatePicker::make('assigned_until')
                            ->label('Assigné jusqu\'au'),
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
