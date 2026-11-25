<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        return Auth::check() ? redirect()->route('portal.dashboard') : view('portal.auth', ['register' => false]);
    }

    public function showRegister()
    {
        abort_unless(config('portal.registration_enabled'), 404);
        return Auth::check() ? redirect()->route('portal.dashboard') : view('portal.auth', ['register' => true]);
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('portal.dashboard');
        }

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        if (! Auth::attemptWhen(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            fn (User $user) => $user->canAccessPanel(Filament::getPanel('admin')),
            $request->boolean('remember'),
        )) {
            throw ValidationException::withMessages(['email' => __('Email atau kata sandi tidak sesuai, atau akun tidak memiliki akses.')]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('portal.dashboard'));
    }

    public function register(Request $request)
    {
        abort_unless(config('portal.registration_enabled'), 404);
        if (Auth::check()) {
            return redirect()->route('portal.dashboard');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = User::create([
            'name' => $data['name'], 'email' => $data['email'],
            'password' => $data['password'], 'level' => Role::User,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
