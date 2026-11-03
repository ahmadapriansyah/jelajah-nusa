<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PaketController extends Controller
{
    /**
     * Beranda & Katalog Daftar Paket Wisata (F9-01, F9-02, W-04, W-05, US-03).
     */
    public function index(Request $request): View
    {
        $destinasi = $request->query('destinasi');
        $layanan   = $request->query('layanan');
        $cari      = $request->query('cari');
        $hargaMax  = $request->query('harga_max');
        $tanggal   = $request->query('tanggal');
        $urutan    = $request->query('urutan', 'terbaru');

        $query = PaketWisata::aktif()->with(['jadwal' => function ($q) {
            $q->where('tanggal_berangkat', '>=', now()->toDateString())
              ->whereIn('status_keberangkatan', ['dijadwalkan', 'dikonfirmasi'])
              ->orderBy('tanggal_berangkat', 'asc');
        }]);

        if (!empty($cari)) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_paket', 'like', "%{$cari}%")
                  ->orWhere('destinasi', 'like', "%{$cari}%")
                  ->orWhere('deskripsi', 'like', "%{$cari}%");
            });
        }

        if (!empty($destinasi)) {
            $query->where('destinasi', 'like', "%{$destinasi}%");
        }

        if (!empty($layanan) && in_array($layanan, ['open_trip', 'paket_reguler'])) {
            $query->where('jenis_layanan', $layanan);
        }

        if (!empty($hargaMax) && is_numeric($hargaMax)) {
            $query->where('harga', '<=', $hargaMax);
        }

        if (!empty($tanggal)) {
            $query->whereHas('jadwal', function ($q) use ($tanggal) {
                $q->whereDate('tanggal_berangkat', '>=', $tanggal);
            });
        }

        if ($urutan === 'termurah') {
            $query->orderBy('harga', 'asc');
        } elseif ($urutan === 'termahal') {
            $query->orderBy('harga', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $paketList = $query->paginate(9)->withQueryString();

        // Daftar destinasi unik untuk filter
        $destinasiList = PaketWisata::aktif()->distinct()->pluck('destinasi');

        return view('wisatawan.paket.index', compact(
            'paketList', 
            'destinasiList', 
            'destinasi', 
            'layanan', 
            'cari', 
            'hargaMax', 
            'tanggal', 
            'urutan'
        ));
    }

    /**
     * Detail Paket Wisata (F9-03, F9-04, W-06, US-04).
     */
    public function show(int $id): View
    {
        $paket = PaketWisata::aktif()
            ->with(['jadwal' => function ($q) {
                $q->where('tanggal_berangkat', '>=', now()->toDateString())
                  ->whereIn('status_keberangkatan', ['dijadwalkan', 'dikonfirmasi'])
                  ->with('kendaraan')
                  ->orderBy('tanggal_berangkat', 'asc');
            }])
            ->findOrFail($id);

        return view('wisatawan.paket.show', compact('paket'));
    }

    /**
     * Menyimpan data paket wisata baru dari form Admin
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga'      => 'required|numeric',
            'foto'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $namaFoto = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            // Penamaan file unik tanpa karakter aneh
            $namaFoto = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            // Simpan file ke folder storage/app/public/paket
            $file->storeAs('public/paket', $namaFoto);
        }

        PaketWisata::create([
            'nama_paket'    => $request->nama_paket,
            'destinasi'     => $request->destinasi,
            'harga'         => $request->harga,
            'durasi'        => $request->durasi,
            'jenis_layanan' => $request->jenis_layanan ?? 'open_trip',
            'deskripsi'     => $request->deskripsi,
            'foto'          => $namaFoto,
            'status'        => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Paket wisata berhasil ditambahkan!');
    }
}