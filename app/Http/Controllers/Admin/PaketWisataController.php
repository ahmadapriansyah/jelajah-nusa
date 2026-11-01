<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\PaketWisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaketWisataController extends Controller
{
    /**
     * Daftar Paket Wisata & Jadwal Terpilih (F2-01, F2-02, Frame 107:1960).
     */
    public function index(Request $request): View
    {
        $search = $request->query('cari');
        $query = PaketWisata::withCount(['jadwal', 'pemesanan']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_paket', 'like', "%{$search}%")
                  ->orWhere('destinasi', 'like', "%{$search}%");
            });
        }

        $paketList = $query->orderBy('id', 'desc')->get();

        // Paket terpilih (default: paket pertama)
        $selectedId = $request->query('selected', $paketList->first()?->id);
        $selectedPaket = $selectedId
            ? PaketWisata::with(['jadwal.kendaraan', 'jadwal.pemesanan'])->find($selectedId)
            : null;

        $kendaraanTersedia = Kendaraan::tersedia()->get();

        return view('admin.paket.index', compact('paketList', 'selectedPaket', 'kendaraanTersedia', 'search'));
    }

    /**
     * Form Tambah Paket Wisata (Frame 107:3139, 107:1648).
     */
    public function create(): View
    {
        $kendaraanList = Kendaraan::tersedia()->get();
        return view('admin.paket.create', compact('kendaraanList'));
    }

    /**
     * Simpan Paket Wisata Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:150',
            'destinasi' => 'required|string|max:100',
            'jenis_layanan' => 'required|in:open_trip,paket_reguler',
            'harga' => 'required|numeric|min:1',
            'durasi_hari' => 'required|integer|min:1|max:30',
            'deskripsi' => 'nullable|string',
            'itinerary' => 'required|array|min:1',
            'itinerary.*.judul' => 'required|string',
            'itinerary.*.aktivitas' => 'required|string',
            'fasilitas' => 'required|string',
            'fasilitas_tidak_termasuk' => 'nullable|string',
            'meeting_point' => 'required|string|max:150',
            'jam_kumpul' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'nama_paket.required' => 'Nama paket wajib diisi.',
            'destinasi.required' => 'Destinasi wajib diisi.',
            'harga.required' => 'Harga paket wajib diisi.',
            'durasi_hari.required' => 'Durasi hari wajib diisi.',
            'itinerary.required' => 'Itinerary per hari wajib diisi minimal 1 hari.',
            'fasilitas.required' => 'Fasilitas termasuk wajib diisi.',
            'meeting_point.required' => 'Meeting point wajib diisi.',
            'jam_kumpul.required' => 'Jam kumpul wajib diisi.',
            'foto.max' => 'Ukuran file foto melebihi 5 MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('paket_foto', 'public');
        }

        // Format itinerary array index {hari, judul, aktivitas}
        $formattedItinerary = [];
        $day = 1;
        foreach ($validated['itinerary'] as $item) {
            $formattedItinerary[] = [
                'hari' => $day++,
                'judul' => $item['judul'],
                'aktivitas' => $item['aktivitas'],
            ];
        }

        $paket = PaketWisata::create([
            'nama_paket' => $validated['nama_paket'],
            'destinasi' => $validated['destinasi'],
            'jenis_layanan' => $validated['jenis_layanan'],
            'harga' => $validated['harga'],
            'durasi_hari' => $validated['durasi_hari'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'itinerary' => $formattedItinerary,
            'fasilitas' => $validated['fasilitas'],
            'fasilitas_tidak_termasuk' => $validated['fasilitas_tidak_termasuk'] ?? null,
            'meeting_point' => $validated['meeting_point'],
            'jam_kumpul' => $validated['jam_kumpul'],
            'foto' => $fotoPath,
            'status_aktif' => true,
        ]);

        LogAktivitas::catat(
            aksi: 'tambah_paket',
            subjekTipe: 'PaketWisata',
            subjekId: $paket->id,
            keterangan: "Paket wisata '{$paket->nama_paket}' ditambahkan."
        );

        return redirect()->route('admin.paket.index', ['selected' => $paket->id])
            ->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Form Ubah Paket Wisata.
     */
    public function edit(int $id): View
    {
        $paket = PaketWisata::findOrFail($id);
        $kendaraanList = Kendaraan::tersedia()->get();
        return view('admin.paket.edit', compact('paket', 'kendaraanList'));
    }

    /**
     * Simpan Perubahan Paket Wisata.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $paket = PaketWisata::findOrFail($id);

        $validated = $request->validate([
            'nama_paket' => 'required|string|max:150',
            'destinasi' => 'required|string|max:100',
            'jenis_layanan' => 'required|in:open_trip,paket_reguler',
            'harga' => 'required|numeric|min:1',
            'durasi_hari' => 'required|integer|min:1|max:30',
            'deskripsi' => 'nullable|string',
            'itinerary' => 'required|array|min:1',
            'itinerary.*.judul' => 'required|string',
            'itinerary.*.aktivitas' => 'required|string',
            'fasilitas' => 'required|string',
            'fasilitas_tidak_termasuk' => 'nullable|string',
            'meeting_point' => 'required|string|max:150',
            'jam_kumpul' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        if ($request->hasFile('foto')) {
            if ($paket->foto) {
                Storage::disk('public')->delete($paket->foto);
            }
            $paket->foto = $request->file('foto')->store('paket_foto', 'public');
        }

        $formattedItinerary = [];
        $day = 1;
        foreach ($validated['itinerary'] as $item) {
            $formattedItinerary[] = [
                'hari' => $day++,
                'judul' => $item['judul'],
                'aktivitas' => $item['aktivitas'],
            ];
        }

        $paket->update([
            'nama_paket' => $validated['nama_paket'],
            'destinasi' => $validated['destinasi'],
            'jenis_layanan' => $validated['jenis_layanan'],
            'harga' => $validated['harga'],
            'durasi_hari' => $validated['durasi_hari'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'itinerary' => $formattedItinerary,
            'fasilitas' => $validated['fasilitas'],
            'fasilitas_tidak_termasuk' => $validated['fasilitas_tidak_termasuk'] ?? null,
            'meeting_point' => $validated['meeting_point'],
            'jam_kumpul' => $validated['jam_kumpul'],
        ]);

        LogAktivitas::catat(
            aksi: 'ubah_paket',
            subjekTipe: 'PaketWisata',
            subjekId: $paket->id,
            keterangan: "Paket wisata '{$paket->nama_paket}' diperbarui."
        );

        return redirect()->route('admin.paket.index', ['selected' => $paket->id])
            ->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Hapus Paket Wisata (BR-12, US-17, T-14).
     */
    public function destroy(int $id): RedirectResponse
    {
        $paket = PaketWisata::findOrFail($id);

        // Jika sudah pernah dipakai pemesanan, tolak penghapusan permanen
        if ($paket->pemesanan()->exists()) {
            return back()->with('error', 'Data ini sudah dipakai pemesanan dan tidak bisa dihapus. Nonaktifkan saja.');
        }

        // Hapus foto jika ada
        if ($paket->foto) {
            Storage::disk('public')->delete($paket->foto);
        }

        $nama = $paket->nama_paket;
        $paket->jadwal()->delete();
        $paket->delete();

        LogAktivitas::catat(
            aksi: 'hapus_paket',
            subjekTipe: 'PaketWisata',
            subjekId: $id,
            keterangan: "Paket wisata '{$nama}' dihapus."
        );

        return redirect()->route('admin.paket.index')->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Aktifkan / Nonaktifkan paket wisata.
     */
    public function toggleAktif(int $id): RedirectResponse
    {
        $paket = PaketWisata::findOrFail($id);
        $paket->status_aktif = !$paket->status_aktif;
        $paket->save();

        $statusTeks = $paket->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        LogAktivitas::catat(
            aksi: 'toggle_paket',
            subjekTipe: 'PaketWisata',
            subjekId: $paket->id,
            keterangan: "Paket wisata '{$paket->nama_paket}' {$statusTeks}."
        );

        return back()->with('sukses', "Paket wisata berhasil {$statusTeks}.");
    }
}
