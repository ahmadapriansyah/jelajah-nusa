<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Tampilkan profil pengguna (F11-02, W-14).
     */
    public function show(Request $request): View
    {
        return view('profil.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Simpan perubahan profil pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['required', 'string', 'regex:/^(?:\+62|62|08)[0-9]{8,13}$/'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'min:8', 'confirmed'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini telah digunakan pengguna lain.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'current_password.required_with' => 'Password saat ini harus diisi untuk mengubah password.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $updateData = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
        ];

        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }
            $updateData['password'] = Hash::make($validated['new_password']);
        }

        $user->update($updateData);

        LogAktivitas::catat(
            aksi: 'update_profil',
            subjekTipe: 'User',
            subjekId: $user->id,
            keterangan: "Pengguna {$user->nama_lengkap} memperbarui data profil.",
            userId: $user->id
        );

        return back()->with('sukses', 'Perubahan berhasil disimpan.');
    }
}
