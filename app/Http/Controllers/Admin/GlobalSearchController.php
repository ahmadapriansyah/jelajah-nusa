<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;
use App\Models\Pemesanan;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    /**
     * Pencarian global di header admin (F13-01, T-23).
     */
    public function search(Request $request): View
    {
        $q = trim($request->query('q', ''));
        $cleanQ = ltrim($q, '#');

        $pemesanan = [];
        $peserta = [];
        $paket = [];

        if (strlen($cleanQ) >= 2) {
            $pemesanan = Pemesanan::with(['user', 'jadwal.paket', 'customTrip'])
                ->where('kode_pemesanan', 'like', "%{$cleanQ}%")
                ->orWhereHas('user', fn($u) => $u->where('nama_lengkap', 'like', "%{$cleanQ}%"))
                ->take(10)
                ->get();

            $peserta = Peserta::with(['pemesanan.user', 'pemesanan.jadwal.paket'])
                ->where('nama_lengkap', 'like', "%{$cleanQ}%")
                ->orWhere('no_identitas', 'like', "%{$cleanQ}%")
                ->take(10)
                ->get();

            $paket = PaketWisata::where('nama_paket', 'like', "%{$cleanQ}%")
                ->orWhere('destinasi', 'like', "%{$cleanQ}%")
                ->take(10)
                ->get();
        }

        return view('admin.search.index', compact('q', 'pemesanan', 'peserta', 'paket'));
    }
}
