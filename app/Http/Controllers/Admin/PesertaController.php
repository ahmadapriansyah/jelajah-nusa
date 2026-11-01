<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Peserta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesertaController extends Controller
{
    /**
     * Cari dan lihat daftar seluruh peserta wisata (F3-05).
     */
    public function index(Request $request): View
    {
        $search = $request->query('cari');
        $query = Peserta::with(['pemesanan.user', 'pemesanan.jadwal.paket', 'tiket'])
            ->orderBy('id', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_identitas', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $pesertaList = $query->paginate(20)->withQueryString();

        return view('admin.peserta.index', compact('pesertaList', 'search'));
    }

    /**
     * Perbarui data peserta jika ada kesalahan pengetikan identitas (F3-05).
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $peserta = Peserta::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'jenis_identitas' => 'required|in:ktp,paspor',
            'no_identitas' => 'required|string|max:30',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $peserta->update($validated);

        LogAktivitas::catat(
            aksi: 'ubah_data_peserta',
            subjekTipe: 'Peserta',
            subjekId: $peserta->id,
            keterangan: "Data peserta ID #{$peserta->id} ({$peserta->nama_lengkap}) diperbarui."
        );

        return back()->with('sukses', 'Perubahan berhasil disimpan.');
    }
}
