<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\City;
use App\Models\ServiceCategory;
use App\Services\BookingReviewService;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification as FilamentNotification;

class PendingVerifications extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Tâches en attente de vérification';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['user', 'provider', 'review'])
                    ->where('status', 'confirmed')
                    ->where('provider_done', true)
                    ->where('admin_verification_status', 'pending')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('provider.name')->label('Prestataire')->searchable(),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('XOF')->sortable(),
                Tables\Columns\IconColumn::make('require_client_review')->label('Notation demandée')
                    ->boolean(),
                Tables\Columns\IconColumn::make('review.id')->label('Noté')
                    ->boolean()
                    ->state(fn (Booking $r) => $r->review()->exists()),
                Tables\Columns\TextColumn::make('provider_done_at')->label('Marqué fait le')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label('Prestataire')
                    ->options(fn () => Provider::query()->orderBy('name')->pluck('name', 'id')->toArray()),
                Tables\Filters\TernaryFilter::make('has_review')
                    ->label('Avec notation')
                    ->queries(
                        true: fn ($query) => $query->whereHas('review'),
                        false: fn ($query) => $query->whereDoesntHave('review'),
                    ),
                Tables\Filters\Filter::make('advanced')
                    ->form([
                        Forms\Components\Select::make('city_id')
                            ->label('Ville')
                            ->options(fn () => City::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable(),
                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie')
                            ->options(fn () => ServiceCategory::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable(),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['city_id'])) {
                            $query->whereHas('provider', function ($q) use ($data) {
                                $q->where('city_id', $data['city_id']);
                            });
                        }
                        if (!empty($data['category_id'])) {
                            $cat = $data['category_id'];
                            $query->where(function ($qq) use ($cat) {
                                $qq->whereHas('provider', function ($p) use ($cat) {
                                    $p->where('category_id', $cat);
                                })->orWhereHas('provider.categories', function ($p) use ($cat) {
                                    $p->where('service_categories.id', $cat);
                                });
                            });
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Vérifier')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Vérifier la qualité du service')
                    ->modalDescription('Cette action valide la qualité du service rendu pour nos statistiques.')
                    ->action(function (Booking $record) {
                        $reviewService = app(BookingReviewService::class);
                        $reviewService->verifyBookingByAdmin($record, auth()->id());
                        
                        FilamentNotification::make()
                            ->title('Travail vérifié')
                            ->body('Le travail du prestataire a été vérifié.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
