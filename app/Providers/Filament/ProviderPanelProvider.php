<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\RedirectIfWrongPanel;
use Illuminate\Validation\ValidationException;
use Filament\Models\Contracts\FilamentUser;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ProviderPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('provider')
            ->path('prestataire')
            ->login(\App\Filament\Pages\Auth\ProviderLogin::class)
            ->brandName('Upfiesta PRO')
            ->databaseNotifications()
            ->homeUrl('/')
            ->emailVerification()
            ->colors([
                'primary' => [
                    50 => '#f0f7ff',
                    100 => '#e0effe',
                    200 => '#bae0fd',
                    300 => '#7cc2fb',
                    400 => '#389ff7',
                    500 => '#004aad', // Logo Blue
                    600 => '#0a79eb',
                    700 => '#0961c0',
                    800 => '#0d519b',
                    900 => '#114481',
                    950 => '#0b2b54',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Provider/Resources'), for: 'App\\Filament\\Provider\\Resources')
            ->discoverPages(in: app_path('Filament/Provider/Pages'), for: 'App\\Filament\\Provider\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Provider/Widgets'), for: 'App\\Filament\\Provider\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Provider\Widgets\AssignmentsStats::class,
                \App\Filament\Provider\Widgets\AccountStatusWidget::class,
                \App\Filament\Provider\Widgets\StatsOverview::class,
            ])
            ->routes(function () {
                Route::post('/login', function () {
                    $data = request('data', []);
                    
                    $data = validator($data, [
                        'email' => 'required|string',
                        'password' => 'required|string',
                        'remember' => 'nullable|boolean',
                    ])->validate();
                    
                    $credentials = [
                        'email' => $data['email'],
                        'password' => $data['password'],
                    ];
                    
                    if (! Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
                        throw ValidationException::withMessages([
                            'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
                        ]);
                    }
                    
                    $user = Filament::auth()->user();
                    
                    if ($user->role === 'client') {
                        Filament::auth()->logout();
                        throw ValidationException::withMessages([
                            'data.email' => 'Ce compte est un compte client. Veuillez vous connecter via l\'espace client.',
                        ]);
                    }
                    
                    if ($user->role === 'admin') {
                        Filament::auth()->logout();
                        throw ValidationException::withMessages([
                            'data.email' => 'Ce compte est un compte administrateur. Veuillez vous connecter via l\'espace administration.',
                        ]);
                    }
                    
                    if (! ($user instanceof FilamentUser) || ! $user->canAccessPanel(Filament::getCurrentPanel())) {
                        Filament::auth()->logout();
                        throw ValidationException::withMessages([
                            'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
                        ]);
                    }
                    
                    session()->regenerate();
                    
                    return app(LoginResponse::class);
                })->name('auth.login.post');
            })
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                RedirectIfWrongPanel::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}


