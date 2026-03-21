<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use App\Models\Provider;
use App\Notifications\ProviderApprovedNotification;
use App\Notifications\ProviderRejectedNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Notification;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Prestataires';

    protected static ?string $modelLabel = 'Prestataire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Générales')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Utilisateur lié')
                            ->relationship('user', 'name', fn ($query) => $query->where('role', 'provider'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du prestataire')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('categories')
                            ->label('Catégories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie Principale')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Contact & Localisation')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->default(fn ($record) => $record?->email ?: $record?->user?->email),
                        Forms\Components\Select::make('city_id')
                            ->label('Ville')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Détails & Vérification')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('providers-logos'),
                        Forms\Components\Select::make('verification_status')
                            ->label('Statut de Vérification')
                            ->options([
                                'pending' => 'En attente',
                                'approved' => 'Approuvé',
                                'rejected' => 'Rejeté',
                            ])
                            ->required()
                            ->helperText('Sélectionnez le statut après avoir vérifier tous les documents ci-dessous.'),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du rejet')
                            ->helperText('Expliquez pourquoi le prestataire a été rejeté (visible par le prestataire).')
                            ->visible(fn ($get) => $get('verification_status') === 'rejected')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Vérifié (Legacy)')
                            ->helperText('Ce champ est conservé pour compatibilité rétroactive.')
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('base_price')
                            ->label('Prix de base')
                            ->numeric()
                            ->suffix('XOF'),
                        Forms\Components\TextInput::make('price_range_max')
                            ->label('Prix max')
                            ->numeric()
                            ->suffix('XOF'),
                    ])->columns(2),

                Forms\Components\Section::make('Documents de Vérification')
                    ->description('Ces documents sont nécessaires pour valider le compte du prestataire.')
                    ->schema([
                        Forms\Components\TextInput::make('cni_number')
                            ->label('Numéro CNI')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('years_of_experience')
                            ->label('Années d\'expérience')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\FileUpload::make('cni_photo_front')
                            ->label('CNI (Recto)')
                            ->image()
                            ->disk('public')
                            ->directory('verification/cni')
                            ->visibility('public')
                            ->required()
                            ->openable()
                            ->downloadable(),
                        Forms\Components\FileUpload::make('cni_photo_back')
                            ->label('CNI (Verso)')
                            ->image()
                            ->disk('public')
                            ->directory('verification/cni')
                            ->visibility('public')
                            ->required()
                            ->openable()
                            ->downloadable(),
                        Forms\Components\Toggle::make('is_company')
                            ->label('Entreprise enregistrée')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('company_registration_number')
                            ->label('Numéro RCCM / NIF')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\FileUpload::make('company_proof_doc_front')
                            ->label('Preuve d\'enregistrement (Recto / Page 1)')
                            ->disk('public')
                            ->directory('verification/company')
                            ->visibility('public')
                            ->openable()
                            ->downloadable(),
                        Forms\Components\FileUpload::make('company_proof_doc_back')
                            ->label('Preuve d\'enregistrement (Verso / Page 2)')
                            ->disk('public')
                            ->directory('verification/company')
                            ->visibility('public')
                            ->openable()
                            ->downloadable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Catégories')
                    ->badge()
                    ->searchable(),
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
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->verification_status !== 'approved')
                    ->action(function (Provider $record) {
                        $userId = auth()->user()?->id;
                        if (!$userId) {
                            return;
                        }
                        
                        // Ensure user is loaded
                        if (!$record->relationLoaded('user')) {
                            $record->load('user');
                        }
                        
                        if (!$record->user) {
                            return;
                        }
                        
                        $record->update([
                            'verification_status' => 'approved',
                            'verified_at' => now(),
                            'verified_by' => $userId,
                            'is_verified' => true,
                        ]);
                        Notification::send($record->user, new ProviderApprovedNotification());
                    }),
                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->verification_status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Raison du rejet')
                            ->helperText('Expliquez pourquoi le prestataire a été rejeté (sera visible par le prestataire).')
                            ->required(),
                    ])
                    ->action(function (Provider $record, array $data) {
                        $userId = auth()->user()?->id;
                        if (!$userId) {
                            return;
                        }
                        
                        // Ensure user is loaded
                        if (!$record->relationLoaded('user')) {
                            $record->load('user');
                        }
                        
                        if (!$record->user) {
                            return;
                        }
                        
                        $record->update([
                            'verification_status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'verified_at' => now(),
                            'verified_by' => $userId,
                            'is_verified' => false,
                        ]);
                        Notification::send($record->user, new ProviderRejectedNotification($record, $data['rejection_reason']));
                    }),
                Action::make('Contacter')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->url(fn ($record) => route('messages.show', ['user' => $record->user_id]))
                    ->openUrlInNewTab(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
