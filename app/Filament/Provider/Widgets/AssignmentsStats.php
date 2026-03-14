<?php

namespace App\Filament\Provider\Widgets;

use App\Models\AssignedService;
use App\Models\Provider;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AssignmentsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $provider = Provider::where('user_id', $user->id)->first();

        if (!$provider) {
            return [];
        }

        $pending = AssignedService::where('provider_id', $provider->id)
            ->where('status', 'pending')
            ->count();

        $accepted = AssignedService::where('provider_id', $provider->id)
            ->where('status', 'accepted')
            ->count();

        $completed = AssignedService::where('provider_id', $provider->id)
            ->where('status', 'completed')
            ->count();

        $rejected = AssignedService::where('provider_id', $provider->id)
            ->where('status', 'rejected')
            ->count();

        $totalRevenue = AssignedService::where('provider_id', $provider->id)
            ->where('status', 'completed')
            ->with('serviceRequest')
            ->get()
            ->sum(fn ($a) => $a->serviceRequest->budget ?? 0);

        return [
            Stat::make('En attente', $pending)
                ->description('Assignations à valider')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->icon('heroicon-m-clock'),

            Stat::make('Acceptées', $accepted)
                ->description('Services en cours')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->icon('heroicon-m-check-circle'),

            Stat::make('Complétées', $completed)
                ->description('Services réalisés')
                ->descriptionIcon('heroicon-m-check')
                ->color('success')
                ->icon('heroicon-m-check'),

            Stat::make('Rejetées', $rejected)
                ->description('Services refusés')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->icon('heroicon-m-x-circle'),

            Stat::make('Revenus totaux', number_format($totalRevenue, 0) . ' XOF')
                ->description('Depuis les services complétés')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->icon('heroicon-m-currency-dollar'),
        ];
    }
}
