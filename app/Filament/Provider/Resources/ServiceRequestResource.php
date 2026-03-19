<?php

namespace App\Filament\Provider\Resources;

use App\Filament\Provider\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Demandes de Services';

    protected static ?string $modelLabel = 'Demande de Service';

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
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
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
                    }),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Date souhaitée')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'processed' => 'Traitée',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('mark_processed')
                    ->label('Marquer comme traitée')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (ServiceRequest $record): bool => $record->getAttribute('status') === 'pending')
                    ->action(fn (ServiceRequest $record) => $record->update(['status' => 'processed'])),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $provider = Auth::user()?->provider;

        if (!$provider) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Show only service requests assigned to this provider by admin
        return parent::getEloquentQuery()
            ->where(function (Builder $query) use ($provider) {
                $query->where('provider_id', $provider->id)
                    ->orWhereHas('assignedServices', function (Builder $q) use ($provider) {
                        $q->where('provider_id', $provider->id);
                    });
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageServiceRequests::route('/'),
            'view' => Pages\ViewServiceRequest::route('/{record}'),
        ];
    }
}

