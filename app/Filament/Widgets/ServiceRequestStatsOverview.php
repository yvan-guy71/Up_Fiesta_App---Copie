<?php

namespace App\Filament\Widgets;

use App\Models\ServiceRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServiceRequestStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Demandes', ServiceRequest::count())
                ->description('Toutes les demandes reçues')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
            Stat::make('Demandes en attente', ServiceRequest::where('status', 'pending')->count())
                ->description('À traiter')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Demandes terminées', ServiceRequest::where('status', 'completed')->count())
                ->description('Besoins satisfaits')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
