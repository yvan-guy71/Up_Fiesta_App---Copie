<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\City;
use App\Models\ServiceCategory;
use App\Services\PayoutService;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification as FilamentNotification;

class PendingPayouts extends BaseWidget
{
    public static function canView(): bool
    {
        return false;
    }

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Versements en attente';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['user', 'provider'])
                    ->where('payment_status', 'paid')
                    ->where('status', 'confirmed')
                    ->where('provider_done', true)
                    ->where('payout_status', 'pending')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('provider.name')->label('Prestataire')->searchable(),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('XOF')->sortable(),
                Tables\Columns\TextColumn::make('provider_amount')->label('À verser')->money('XOF')->sortable(),
                Tables\Columns\TextColumn::make('provider_done_at')->label('Marqué fait le')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label('Prestataire')
                    ->options(fn () => Provider::query()->orderBy('name')->pluck('name', 'id')->toArray()),
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
                Tables\Actions\Action::make('payout')
                    ->label('Verser au prestataire')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Booking $record) {
                        $ok = PayoutService::transfer($record);
                        if ($ok) {
                            FilamentNotification::make()
                                ->title('Versement effectué')
                                ->body("Réservation #{$record->id} versée au prestataire.")
                                ->success()
                                ->send();
                        } else {
                            FilamentNotification::make()
                                ->title('Échec du versement')
                                ->body("Impossible de verser la réservation #{$record->id}. Consultez les logs.")
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Exporter CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $rows = [];
                            $rows[] = ['#', 'Client', 'Prestataire', 'Total', 'À verser', 'Marqué fait le'];
                            foreach ($records as $b) {
                                $rows[] = [
                                    $b->id,
                                    $b->user->name ?? '',
                                    $b->provider->name ?? '',
                                    (string) $b->total_price,
                                    (string) $b->provider_amount,
                                    $b->provider_done_at ? $b->provider_done_at->format('Y-m-d H:i') : '',
                                ];
                            }
                            $csv = '';
                            foreach ($rows as $r) {
                                $csv .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $r)) . "\n";
                            }
                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, 'versements_en_attente.csv', [
                                'Content-Type' => 'text/csv',
                            ]);
                        }),
                ]),
            ]);
    }
}
