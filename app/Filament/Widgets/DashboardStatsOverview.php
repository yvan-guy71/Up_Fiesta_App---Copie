<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Booking;
use App\Models\Provider;

class DashboardStatsOverview extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-stats-overview';

    public function getViewData(): array
    {
        return [
            'users_count' => User::count(),
            'bookings_count' => Booking::count(),
            'providers_count' => Provider::count(),
        ];
    }
}
