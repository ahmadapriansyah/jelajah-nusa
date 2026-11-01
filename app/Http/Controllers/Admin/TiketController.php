<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Services\TicketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TiketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    /**
     * Manajemen Tiket & Verifikasi Keaslian (F6, Frame 107:2678, US-14, T-13).
     */
    public function index(Request $request): View
    {
        $search = $request->query('cari');
        $query = Tiket::with(['peserta', 'pemesanan.user', 'pemesanan.jadwal.paket', 'pemesanan.customTrip'])
            ->orderBy('id', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_tiket', 'like', "%{$search}%")
                  ->orWhereHas('peserta', function ($pq) use ($search) {
                      $pq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $tiketList = $query->paginate(15)->withQueryString();

        $verifikasiKode = $request->query('verifikasi_kode');
        $hasilVerifikasi = null;
        if (!empty($verifikasiKode)) {
            $hasilVerifikasi = $this->ticketService->verifyTicket($verifikasiKode);
        }

        return view('admin.tiket.index', compact('tiketList', 'hasilVerifikasi', 'verifikasiKode', 'search'));
    }

    /**
     * Cetak Tiket Digital dalam format PDF resmi (F6-03).
     */
    public function cetak(int $id): Response
    {
        $tiket = Tiket::with(['peserta', 'pemesanan.user', 'pemesanan.jadwal.paket', 'pemesanan.customTrip'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('tiket.pdf', compact('tiket'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Tiket_{$tiket->kode_tiket}.pdf");
    }
}
