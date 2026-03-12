<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Forms;
use Filament\Forms\Form;

class ProviderLogin extends BaseLogin
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
           ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
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

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $identifier = trim($data['email']);
        $password = $data['password'];
        $isPhone = preg_match('/^\+?[0-9\s\-().]+$/', $identifier) === 1;
        if ($isPhone) {
            $normalized = preg_replace('/[^0-9+]/', '', $identifier);
            $user = \App\Models\User::where('phone', $normalized)->first();
            if ($user) {
                return [
                    'email' => $user->email,
                    'password' => $password,
                ];
            }
        }
        return [
            'email' => $identifier,
            'password' => $password,
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
