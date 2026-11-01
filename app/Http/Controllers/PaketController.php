<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaketController extends Controller
{
    /**
     * Beranda & Katalog Daftar Paket Wisata (F9-01, F9-02, W-04, W-05, US-03).
     */
    public function index(Request $request): View
    {
        $destinasi = $request->query('destinasi');
        $layanan = $request->query('layanan');
        $cari = $request->query('cari');
        $hargaMax = $request->query('harga_max');
        $tanggal = $request->query('tanggal');
        $urutan = $request->query('urutan', 'terbaru');

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

        return view('wisatawan.paket.index', compact('paketList', 'destinasiList', 'destinasi', 'layanan', 'cari', 'hargaMax', 'tanggal', 'urutan'));
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
}
