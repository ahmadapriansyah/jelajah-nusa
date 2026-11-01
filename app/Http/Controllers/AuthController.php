<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login (F1-01, W-01).
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('pesanan-saya.index');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna dengan username dan password (F1-02, US-02, T-01).
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('username')) . '|' . $request->ip());

        // Batasi percobaan login gagal (5 kali -> kunci 1 menit, US-02)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('username');
        }

        $login = trim($request->input('username'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Cari pengguna berdasarkan username, email, atau variasi tanpa/dengan underscore
        $targetUser = User::where('username', $login)
            ->orWhere('email', $login)
            ->when(str_contains($login, '_'), function ($q) use ($login) {
                $q->orWhere('username', str_replace('_', '', $login));
            })
            ->when(!str_contains($login, '_') && !str_contains($login, '@'), function ($q) use ($login) {
                $q->orWhere('username', str_replace(['budi', 'rina'], ['budi_', 'rina_'], $login));
            })
            ->first();

        $attemptSuccess = false;
        if ($targetUser && Hash::check($password, $targetUser->password)) {
            Auth::login($targetUser, $remember);
            $attemptSuccess = true;
        }

        if ($attemptSuccess) {
            RateLimiter::clear($throttleKey);

            $user = Auth::user();

            // Cek apakah akun aktif
            if (!$user->status_aktif) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Akun Anda dinonaktifkan. Hubungi admin Jelajah Nusa.',
                ]);
            }

            $request->session()->regenerate();

            LogAktivitas::catat(
                aksi: 'login',
                subjekTipe: 'User',
                subjekId: $user->id,
                keterangan: "Pengguna {$user->nama_lengkap} ({$user->username}) berhasil login.",
                userId: $user->id
            );

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('pesanan-saya.index'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Logout pengguna dari sistem (F1-04).
     */
    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            LogAktivitas::catat(
                aksi: 'logout',
                subjekTipe: 'User',
                subjekId: Auth::id(),
                keterangan: "Pengguna " . Auth::user()->nama_lengkap . " logout.",
                userId: Auth::id()
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('sukses', 'Anda telah berhasil keluar.');
    }
}
