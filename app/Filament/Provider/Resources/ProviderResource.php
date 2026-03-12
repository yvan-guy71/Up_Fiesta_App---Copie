<?php

namespace App\Filament\Provider\Resources;

use App\Models\Provider;
use App\Models\ProviderMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Mon Profil';

    protected static ?string $modelLabel = 'Profil Prestataire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de l\'entreprise')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom commercial')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('categories')
                            ->label('Catégories de service')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie principale')
                            ->relationship('category', 'name')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description de vos services')
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
                            ->label('Email professionnel')
                            ->email(),
                        Forms\Components\TextInput::make('website')
                            ->label('Site web / Réseaux sociaux')
                            ->url(),
                        Forms\Components\Select::make('city_id')
                            ->label('Ville')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Tarification & Visuels')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo ou Photo de couverture')
                            ->image()
                            ->directory('providers-logos'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('base_price')
                                    ->label('Prix de base')
                                    ->numeric()
                                    ->suffix('XOF'),
                                Forms\Components\TextInput::make('price_range_max')
                                    ->label('Prix maximum')
                                    ->numeric()
                                    ->suffix('XOF'),
                            ]),
                    ]),

                Forms\Components\Section::make('Documents de Vérification')
                    ->description('Ces documents ont été fournis lors de votre inscription.')
                    ->schema([
                        Forms\Components\TextInput::make('cni_number')
                            ->label('Numéro CNI / Passeport')
                            ->disabled(),
                        Forms\Components\FileUpload::make('cni_photo_front')
                            ->label('CNI / Passeport (Recto)')
                            ->image()
                            ->directory('verification/cni')
                            ->disabled(),
                        Forms\Components\FileUpload::make('cni_photo_back')
                            ->label('CNI / Passeport (Verso)')
                            ->image()
                            ->directory('verification/cni')
                            ->disabled(),
                        Forms\Components\TextInput::make('company_registration_number')
                            ->label('Numéro RCCM / NIF')
                            ->visible(fn (Provider $record) => $record->is_company)
                            ->disabled(),
                        Forms\Components\FileUpload::make('company_proof_doc_front')
                            ->label('Preuve d\'enregistrement (Recto / Page 1)')
                            ->directory('verification/company')
                            ->visible(fn (Provider $record) => $record->is_company)
                            ->disabled(),
                        Forms\Components\FileUpload::make('company_proof_doc_back')
                            ->label('Preuve d\'enregistrement (Verso / Page 2)')
                            ->directory('verification/company')
                            ->visible(fn (Provider $record) => $record->is_company)
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Galerie Photos & Vidéos')
                    ->description('Ajoutez des visuels de vos réalisations pour attirer plus de clients.')
                    ->schema([
                        Forms\Components\Repeater::make('media')
                            ->relationship('media')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Type de média')
                                    ->options([
                                        'image' => 'Image',
                                        'video' => 'Vidéo (URL YouTube/Vimeo)',
                                    ])
                                    ->required()
                                    ->reactive(),
                                Forms\Components\FileUpload::make('file_path')
                                    ->label('Fichier Image')
                                    ->image()
                                    ->directory('providers-gallery')
                                    ->required()
                                    ->visible(fn (callable $get) => $get('type') === 'image'),
                                Forms\Components\TextInput::make('file_path')
                                    ->label('Lien de la vidéo')
                                    ->url()
                                    ->placeholder('https://www.youtube.com/watch?v=...')
                                    ->required()
                                    ->visible(fn (callable $get) => $get('type') === 'video'),
                                Forms\Components\TextInput::make('title')
                                    ->label('Légende / Titre')
                                    ->maxLength(255),
                                Forms\Components\Hidden::make('sort_order')
                                    ->default(0),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nouveau média')
                            ->collapsible()
                            ->grid(2)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un média'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Aperçu')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Vérifié')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Provider\Resources\ProviderResource\Pages\ManageProviders::route('/'),
        ];
    }
}
