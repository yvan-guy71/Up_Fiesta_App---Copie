<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Demandes de services';
    
    protected static ?string $modelLabel = 'Demande de service';
    
    protected static ?string $pluralModelLabel = 'Demandes de services';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Client')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name', fn ($query) => $query->where('role', 'client'))
                            ->required()
                            ->searchable()
                            ->label('Client'),
                        Forms\Components\Select::make('provider_id')
                            ->relationship('provider', 'name')
                            ->searchable()
                            ->label('Prestataire concerné'),
                        Forms\Components\Select::make('event_id')
                            ->label('Événement lié')
                            ->relationship('event', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('Aucun événement associé'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Détails de la demande')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'service' => 'Demande de service',
                                'event' => 'Demande d\'événement',
                            ])
                            ->required()
                            ->default('service')
                            ->label('Type de demande'),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->label('Sujet'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'En attente',
                                'processed' => 'Traitée',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                            ])
                            ->required()
                            ->default('pending')
                            ->label('Statut'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->label('Description des besoins'),
                    ])->columns(2),

                Forms\Components\Section::make('Informations complémentaires')
                    ->schema([
                        Forms\Components\DateTimePicker::make('event_date')
                            ->label('Date de l\'événement'),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255)
                            ->label('Lieu'),
                        Forms\Components\TextInput::make('budget')
                            ->numeric()
                            ->prefix('XOF')
                            ->label('Budget estimé'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'service' => 'Service',
                        'event' => 'Événement',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Prestataire')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Aucun prestataire spécifié'),
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
                Tables\Columns\TextColumn::make('event_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Date événement'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date de demande'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
