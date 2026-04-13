<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\RedirectIfWrongPanel;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('Upfiesta-kygj')
            ->login(\App\Filament\Pages\Auth\AdminLogin::class)
            ->brandName('Upfiesta ADMIN')
            ->databaseNotifications()
            ->homeUrl('/')
            ->emailVerification()
            ->profile()
            ->authGuard('web')
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
            ->font('Poppins')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\AssignmentsOverview::class,
                \App\Filament\Widgets\RecentAssignments::class,
                \App\Filament\Widgets\ServiceRequestStatsOverview::class,
                \App\Filament\Widgets\PendingPayoutsOverview::class,
                \App\Filament\Widgets\LatestServiceRequests::class,
                \App\Filament\Widgets\PendingVerifications::class,
                \App\Filament\Widgets\PendingPayouts::class,
            ])
            ->navigationItems([
                NavigationItem::make('Voir le site public')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-globe-alt')
                    ->group('Navigation')
                    ->sort(100),
            ])
            ->routes(function () {
                Route::post('/login', function () {
                    // Récupérer les données du formulaire
                    $data = request('data', []);
                    
                    // Valider les données
                    $data = validator($data, [
                        'email' => 'required|string',
                        'password' => 'required|string',
                        'remember' => 'nullable|boolean',
                    ])->validate();
                    
                    $credentials = [
                        'email' => $data['email'],
                        'password' => $data['password'],
                    ];
                    
                    // Tentative d'authentification
                    if (! Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
                        throw ValidationException::withMessages([
                            'data.email' => 'Identifiants incorrects.',
                        ]);
                    }
                    
                    $user = Filament::auth()->user();
                    
                    // Vérifier le rôle et rediriger si nécessaire
                    if ($user->role === 'provider') {
                        Filament::auth()->logout();
                        throw ValidationException::withMessages([
                            'data.email' => 'Ce compte est un compte prestataire. Veuillez vous connecter via l\'espace professionnel.',
                        ]);
                    }
                    
                    if ($user->role === 'client') {
                        Filament::auth()->logout();
                        throw ValidationException::withMessages([
                            'data.email' => 'Ce compte est un compte client. Veuillez vous connecter via l\'espace client.',
                        ]);
                    }
                    
                    // Vérifier l'accès au panel
                    if (! ($user instanceof FilamentUser) || ! $user->canAccessPanel(Filament::getCurrentPanel())) {
                        Filament::auth()->logout();
                        throw ValidationException::withMessages([
                            'data.email' => 'Identifiants incorrects ou accès non autorisé à l\'administration.',
                        ]);
                    }
                    
                    session()->regenerate();
                    
                    return app(LoginResponse::class);
                });
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



