<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    /**
     * Halaman Pengaturan Sistem & Manajemen Pengguna (F11-03, F12-01).
     */
    public function index(): View
    {
        $pengaturan = Pengaturan::getSettings();
        $adminUsers = User::where('role', 'admin')->get();
        $wisatawanUsers = User::where('role', 'wisatawan')->paginate(15);
        $logAktivitas = LogAktivitas::with('user')->latest()->take(20)->get();

        return view('admin.pengaturan.index', compact('pengaturan', 'adminUsers', 'wisatawanUsers', 'logAktivitas'));
    }

    /**
     * Simpan Pengaturan Rekening & Batas Waktu Bayar (F12-01).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $pengaturan = Pengaturan::getSettings();

        $validated = $request->validate([
            'nama_bank' => 'required|string|max:50',
            'no_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:100',
            'batas_bayar_jam' => 'required|integer|min:1|max:168',
        ], [
            'nama_bank.required' => 'Nama bank tujuan transfer wajib diisi.',
            'no_rekening.required' => 'Nomor rekening wajib diisi.',
            'atas_nama.required' => 'Atas nama rekening wajib diisi.',
            'batas_bayar_jam.required' => 'Batas waktu bayar wajib diisi.',
        ]);

        $pengaturan->update($validated);

        LogAktivitas::catat('update_pengaturan', 'Pengaturan', $pengaturan->id, 'Pengaturan sistem & rekening transfer diperbarui.');

        return back()->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Tambah Akun Admin Baru (F11-03).
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'no_hp' => 'required|string|max:20',
            'username' => 'required|alpha_dash|min:4|max:50|unique:users,username',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status_aktif' => true,
        ]);

        LogAktivitas::catat('tambah_admin', 'User', $user->id, "Akun admin baru '{$user->username}' dibuat.");

        return back()->with('sukses', 'Akun admin baru berhasil dibuat.');
    }

    /**
     * Aktifkan / Nonaktifkan Pengguna (F11-03, US-02).
     */
    public function toggleUserStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->status_aktif = !$user->status_aktif;
        $user->save();

        $statusTeks = $user->status_aktif ? 'diaktifkan' : 'dinonaktifkan';
        LogAktivitas::catat('toggle_user_status', 'User', $user->id, "Akun pengguna '{$user->username}' {$statusTeks}.");

        return back()->with('sukses', "Akun pengguna '{$user->username}' berhasil {$statusTeks}.");
    }
}
