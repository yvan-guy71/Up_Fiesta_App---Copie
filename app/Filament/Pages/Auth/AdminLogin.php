<?php

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;

class AdminLogin extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->label(__('filament-panels::pages/auth/login.form.email.label'))
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus(),
                Forms\Components\TextInput::make('password')
                    ->label(__('filament-panels::pages/auth/login.form.password.label'))
                    ->password()
                    ->required()
                    ->revealable(),
                Forms\Components\Checkbox::make('remember')
                    ->label(__('filament-panels::pages/auth/login.form.remember.label')),
                Forms\Components\ViewField::make('home_link')
                    ->view('filament.pages.auth.login-extra'),
            ])
            ->statePath('data');
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $data = $this->form->getState();
        } catch (\Throwable $e) {
            $this->throwFailureValidationException();
        }

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

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

        if (! ($user instanceof FilamentUser) || ! $user->canAccessPanel(Filament::getCurrentPanel())) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'Identifiants incorrects ou accès non autorisé à l\'administration.',
        ]);
    }
}
