<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran wisatawan (F8-01, W-02).
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('pesanan-saya.index');
        }

        return view('auth.register');
    }

    /**
     * Tangani proses registrasi akun baru (F8-02, F8-03, F8-04, US-01, T-03).
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'regex:/^(?:\+62|62|08)[0-9]{8,13}$/'],
            'username' => ['required', 'string', 'alpha_dash', 'min:4', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Format nomor HP tidak valid (gunakan format Indonesia 08xx atau +62xx).',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda strip, dan garis bawah.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.unique' => 'Username ini sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->input('nama_lengkap'),
            'email' => $request->input('email'),
            'no_hp' => $request->input('no_hp'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'role' => 'wisatawan',
            'status_aktif' => true,
        ]);

        Auth::login($user);

        LogAktivitas::catat(
            aksi: 'registrasi',
            subjekTipe: 'User',
            subjekId: $user->id,
            keterangan: "Akun wisatawan baru mendaftar: {$user->nama_lengkap} ({$user->username})",
            userId: $user->id
        );

        return redirect()->route('pesanan-saya.index')->with('sukses', 'Akun berhasil dibuat. Selamat datang di Jelajah Nusa.');
    }
}
