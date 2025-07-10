<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class LoginCustom extends Login
{
    public ?string $login = null;
    public ?string $password = null;
    public bool $remember = false;

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\TextInput::make('login') // Ganti dari email ke login
                        ->label('Email/NBM')
                        ->required()
                        ->autofocus()
                        ->autocomplete()
                        ->type('text'), // ← ini penting agar validasi @ tidak muncul

                    Forms\Components\TextInput::make('password')
                        ->label(__('filament-panels::pages/auth/login.form.password.label'))
                        ->password()
                        ->required()
                        ->autocomplete('current-password'),
                    Forms\Components\Checkbox::make('remember')
                        ->label(__('filament-panels::pages/auth/login.form.remember.label')),
                ]),
        ];
    }

    public function authenticate(): LoginResponse
    {
        $credentials = [
            'password' => $this->password,
        ];

        if (filter_var($this->login, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $this->login;
        } else {
            $credentials['nbm'] = $this->login;
        }

        // Tambahkan pengecekan is_active
        $userQuery = \App\Models\User::query();

        if (isset($credentials['email'])) {
            $userQuery->where('email', $credentials['email']);
        } else {
            $userQuery->where('nbm', $credentials['nbm']);
        }

        $user = $userQuery->first();

        if (! $user || ! $user->is_active) {
            $this->addError('login', 'Akun Anda tidak aktif.');
            return app(LoginResponse::class);
        }

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            return app(LoginResponse::class);
        }

        $this->addError('login', __('auth.failed'));
        return app(LoginResponse::class);
    }
}
