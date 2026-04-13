<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;


use App\Models\Provider;
use App\Notifications\ProviderApprovedNotification;
use App\Notifications\ProviderRejectedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $navigationLabel = 'Prestataires';
    
    protected static ?string $modelLabel = 'Prestataire';
    
    protected static ?string $pluralModelLabel = 'Prestataires';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Générales')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Utilisateur associé'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom commercial'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email de contact'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->label('Téléphone'),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->label('Catégorie principale'),
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->required()
                            ->label('Ville'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->label('Description'),
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->disk('public')
                            ->directory('providers/logos')
                            ->label('Logo / Photo de profil'),
                    ])->columns(2),

                Forms\Components\Section::make('Vérification & Statut')
                    ->schema([
                        Forms\Components\Select::make('verification_status')
                            ->options([
                                'pending' => 'En attente',
                                'approved' => 'Approuvé',
                                'rejected' => 'Rejeté',
                            ])
                            ->required()
                            ->label('Statut de vérification')
                            ->reactive(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du rejet')
                            ->visible(fn ($get) => $get('verification_status') === 'rejected')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Est certifié (Badge vérifié)')
                            ->helperText('Cochez pour afficher le badge de certification sur le profil'),
                    ])->columns(2),

                Forms\Components\Section::make('Documents & Expérience')
                    ->schema([
                        Forms\Components\Toggle::make('is_company')
                            ->label('Est une entreprise')
                            ->reactive(),
                        Forms\Components\TextInput::make('company_registration_number')
                            ->label('Numéro RCCM')
                            ->visible(fn ($get) => $get('is_company')),
                        Forms\Components\TextInput::make('cni_number')
                            ->label('Numéro CNI/Passeport')
                            ->hidden(fn ($get) => $get('is_company')),
                        Forms\Components\TextInput::make('years_of_experience')
                            ->numeric()
                            ->label('Années d\'expérience'),
                        Forms\Components\FileUpload::make('cni_photo_front')
                            ->image()
                            ->disk('public')
                            ->directory('verification/cni')
                            ->label('CNI (Recto)')
                            ->openable()
                            ->downloadable()
                            ->hidden(fn ($get) => $get('is_company')),
                        Forms\Components\FileUpload::make('cni_photo_back')
                            ->image()
                            ->disk('public')
                            ->directory('verification/cni')
                            ->label('CNI (Verso)')
                            ->openable()
                            ->downloadable()
                            ->hidden(fn ($get) => $get('is_company')),
                        Forms\Components\FileUpload::make('company_proof_doc_front')
                            ->image()
                            ->disk('public')
                            ->directory('verification/company')
                            ->label('Document entreprise (Recto)')
                            ->openable()
                            ->downloadable()
                            ->visible(fn ($get) => $get('is_company')),
                        Forms\Components\FileUpload::make('company_proof_doc_back')
                            ->image()
                            ->disk('public')
                            ->directory('verification/company')
                            ->label('Document entreprise (Verso)')
                            ->openable()
                            ->downloadable()
                            ->visible(fn ($get) => $get('is_company')),
                    ])->columns(2),

                Forms\Components\Section::make('Tarification')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->numeric()
                            ->prefix('XOF')
                            ->label('Prix minimum'),
                        Forms\Components\TextInput::make('price_range_max')
                            ->numeric()
                            ->prefix('XOF')
                            ->label('Prix maximum'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie principale')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ville')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->getStateUsing(fn ($record) => $record->email ?: $record->user?->email)
                    ->searchable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('verification_status')
                    ->label('Statut')
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('is_verified')
                    ->label('Vérification')
                    ->formatStateUsing(function (bool $state, Provider $record): string {
                        if ($record->verification_status === 'pending') {
                            return 'Non vérifié';
                        }
                        return $state ? 'Certifié' : 'Non certifié';
                    })
                    ->color(function (bool $state, Provider $record): string {
                        if ($record->verification_status === 'pending') {
                            return 'warning';
                        }
                        return $state ? 'success' : 'danger';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Prix de base')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Statut de Vérification')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Ville')
                    ->relationship('city', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie principale')
                    ->relationship('category', 'name')
                    ->searchable(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Inscrit du'),
                        \Filament\Forms\Components\DatePicker::make('to')->label('Inscrit jusqu\'au'),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['to'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Provider $record) => $record->verification_status !== 'approved')
                    ->action(function (Provider $record) {
                        $record->update([
                            'verification_status' => 'approved',
                            'is_verified' => true,
                            'verified_at' => now(),
                            'verified_by' => Auth::id(),
                        ]);

                        if ($record->user) {
                            $record->user->notify(new ProviderApprovedNotification($record));
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Prestataire approuvé')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du rejet')
                            ->required(),
                    ])
                    ->visible(fn (Provider $record) => $record->verification_status !== 'approved')
                    ->action(function (Provider $record, array $data) {
                        $record->update([
                            'verification_status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'is_verified' => false,
                        ]);

                        if ($record->user) {
                            $record->user->notify(new ProviderRejectedNotification($record, $data['rejection_reason']));
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Prestataire rejeté')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EventsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}