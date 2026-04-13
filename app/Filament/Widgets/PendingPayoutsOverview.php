<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingPayoutsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return false;
    }

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $query = Booking::query()
            ->where('payment_status', 'paid')
            ->where('status', 'confirmed')
            ->where('provider_done', true)
            ->where('payout_status', 'pending');

        $count = (int) $query->count();
        $total = (int) $query->sum('provider_amount');

        return [
            Stat::make('Versements en attente', $count)
                ->description('Réservations prêtes à être versées')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Total à verser', number_format($total, 0, ',', ' ') . ' XOF')
                ->description('Somme des parts prestataires')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
