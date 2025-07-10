<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginCustom extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('login')
                    ->label('Email atau NBM')
                    ->required()
                    ->autofocus(),

                TextInput::make('password')
                    ->label(__('filament-panels::pages/auth/login.form.password.label'))
                    ->password()
                    ->required(),

                Checkbox::make('remember')
                    ->label(__('filament-panels::pages/auth/login.form.remember.label')),
            ])
            ->statePath('data');
    }

    /**
     * Menimpa method authenticate untuk menambahkan logika pengecekan yang lebih spesifik.
     */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();
        $login = $data['login'];
        $password = $data['password'];
        $remember = $data['remember'];

        // 1. Cari user berdasarkan email atau nbm
        $user = User::query()
            ->where('email', $login)
            ->orWhere('nbm', $login)
            ->first();

        // 2. Jika user tidak ditemukan ATAU password salah, kirim pesan error yang sama.
        // Ini adalah praktik keamanan yang baik untuk mencegah user enumeration.
        if (! $user || ! Hash::check($password, $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        // 3. Jika kredensial sudah benar, BARU periksa apakah akunnya aktif.
        // Ini memberikan pesan error yang spesifik dan jelas.
        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'data.login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        // 4. Jika semua pengecekan lolos, loginkan user.
        Auth::login($user, $remember);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
