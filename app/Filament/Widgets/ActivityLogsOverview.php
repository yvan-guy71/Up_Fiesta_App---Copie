<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\ActivityLog;

class ActivityLogsOverview extends Widget
{
    protected static string $view = 'filament.widgets.activity-logs-overview';

    public function getViewData(): array
    {
        return [
            'logs' => ActivityLog::latest()->take(10)->with('user')->get(),
        ];
    }
}
