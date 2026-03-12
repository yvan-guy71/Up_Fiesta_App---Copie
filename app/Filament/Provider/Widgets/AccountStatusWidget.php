<?php

namespace App\Filament\Provider\Widgets;

use App\Models\Provider;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AccountStatusWidget extends Widget
{
    protected static string $view = 'filament.provider.widgets.account-status-widget';

    protected static ?int $sort = -1; // Toujours en haut

    protected int | string | array $columnSpan = 'full';

    public function getProvider()
    {
        return Provider::where('user_id', Auth::id())->first();
    }

    public static function canView(): bool
    {
        $provider = Provider::where('user_id', Auth::id())->first();
        return $provider && !$provider->is_verified;
    }
}
