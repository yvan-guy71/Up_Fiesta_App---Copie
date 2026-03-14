<?php

namespace App\Filament\Widgets;

use App\Models\AssignedService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssignmentsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total = AssignedService::count();
        $pending = AssignedService::where('status', 'pending')->count();
        $accepted = AssignedService::where('status', 'accepted')->count();
        $completed = AssignedService::where('status', 'completed')->count();
        $rejected = AssignedService::where('status', 'rejected')->count();

        $totalBudget = AssignedService::where('status', 'completed')
            ->with('serviceRequest')
            ->get()
            ->sum(fn ($a) => $a->serviceRequest->budget ?? 0);

        $conversionRate = $total > 0 ? round(($accepted / $total) * 100, 1) : 0;

        return [
            Stat::make('Total assignations', $total)
                ->description('Tous les services assignés')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->icon('heroicon-m-arrow-path'),

            Stat::make('En attente', $pending)
                ->description('Réponses attendues')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->icon('heroicon-m-clock'),

            Stat::make('Acceptées', $accepted)
                ->description('Taux conversion: ' . $conversionRate . '%')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->icon('heroicon-m-check-circle'),

            Stat::make('Complétées', $completed)
                ->description('Services finalisés')
                ->descriptionIcon('heroicon-m-check')
                ->color('info')
                ->icon('heroicon-m-check'),

            Stat::make('Rejetées', $rejected)
                ->description('Services refusés')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->icon('heroicon-m-x-circle'),

            Stat::make('Chiffre complété', number_format($totalBudget, 0) . ' XOF')
                ->description('Budget des services complétés')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->icon('heroicon-m-currency-dollar'),
        ];
    }
}
