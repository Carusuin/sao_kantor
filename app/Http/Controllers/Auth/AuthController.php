<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Cek apakah user aktif
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak terdaftar dalam sistem.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda sedang tidak aktif. Hubungi administrator.'],
            ]);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password yang Anda masukkan salah.'],
            ]);
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user->name . '!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}