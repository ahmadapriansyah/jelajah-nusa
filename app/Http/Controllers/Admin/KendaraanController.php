<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    /**
     * Daftar Kelola Armada Kendaraan (F2-05, Frame 107:2972).
     */
    public function index(): View
    {
        $kendaraanList = Kendaraan::withCount('jadwal')->orderBy('id', 'desc')->get();

        return view('admin.kendaraan.index', compact('kendaraanList'));
    }

    /**
     * Simpan Kendaraan Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_kendaraan' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1|max:100',
            'plat_nomor' => 'nullable|string|max:15',
            'status' => 'required|in:tersedia,tidak_tersedia',
        ], [
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib diisi.',
            'kapasitas.required' => 'Kapasitas kursi wajib diisi.',
            'kapasitas.min' => 'Kapasitas minimal 1 kursi.',
        ]);

        $kendaraan = Kendaraan::create($validated);

        LogAktivitas::catat(
            aksi: 'tambah_kendaraan',
            subjekTipe: 'Kendaraan',
            subjekId: $kendaraan->id,
            keterangan: "Kendaraan '{$kendaraan->jenis_kendaraan}' ditambahkan."
        );

        return back()->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Perbarui Data Kendaraan.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $validated = $request->validate([
            'jenis_kendaraan' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1|max:100',
            'plat_nomor' => 'nullable|string|max:15',
            'status' => 'required|in:tersedia,tidak_tersedia',
        ]);

        $kendaraan->update($validated);

        LogAktivitas::catat(
            aksi: 'ubah_kendaraan',
            subjekTipe: 'Kendaraan',
            subjekId: $kendaraan->id,
            keterangan: "Kendaraan '{$kendaraan->jenis_kendaraan}' diperbarui."
        );

        return back()->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Hapus Kendaraan (BR-12).
     */
    public function destroy(int $id): RedirectResponse
    {
        $kendaraan = Kendaraan::findOrFail($id);

        if ($kendaraan->jadwal()->exists() || $kendaraan->customTrips()->exists()) {
            return back()->with('error', 'Data ini sudah dipakai pemesanan dan tidak bisa dihapus. Nonaktifkan saja.');
        }

        $nama = $kendaraan->jenis_kendaraan;
        $kendaraan->delete();

        LogAktivitas::catat(
            aksi: 'hapus_kendaraan',
            subjekTipe: 'Kendaraan',
            subjekId: $id,
            keterangan: "Kendaraan '{$nama}' dihapus."
        );

        return back()->with('sukses', 'Perubahan berhasil disimpan.');
    }
}
