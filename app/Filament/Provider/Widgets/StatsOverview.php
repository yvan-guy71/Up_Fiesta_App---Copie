<?php

namespace App\Filament\Provider\Widgets;

use App\Models\Booking;
use App\Models\Provider;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $provider = Provider::where('user_id', Auth::id())->first();

        if (!$provider) {
            return [];
        }

        $totalBookings = $provider->bookings()->count();
        $pendingBookings = $provider->bookings()->where('status', 'pending')->count();
        $completedBookings = $provider->bookings()->where('status', 'completed')->count();

        return [
            Stat::make('Total Réservations', $totalBookings)
                ->description('Toutes les réservations confondues')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            Stat::make('Réservations en attente', $pendingBookings)
                ->description('Nécessitent votre attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Services terminés', $completedBookings)
                ->description('Missions réalisées avec succès')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
