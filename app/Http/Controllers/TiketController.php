<?php

namespace App\Http\Controllers;

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
     * Unduh Tiket / Voucher Digital oleh Wisatawan (F10-04).
     */
    public function unduh(Request $request, int $id): Response
    {
        $user = $request->user();
        $tiket = Tiket::with(['peserta', 'pemesanan.user', 'pemesanan.jadwal.paket', 'pemesanan.customTrip'])
            ->findOrFail($id);

        // Hanya pemilik pemesanan atau admin yang dapat mengunduh tiket
        if (!$user->isAdmin() && $tiket->pemesanan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $pdf = Pdf::loadView('tiket.pdf', compact('tiket'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Tiket_JelajahNusa_{$tiket->kode_tiket}.pdf");
    }

    /**
     * Halaman Publik Verifikasi Keaslian Tiket (F6-04, W-15, US-14, T-13).
     * Dapat diakses umum lewat QR Code atau input manual kode tiket.
     */
    public function verifikasiPublik(Request $request, ?string $kode = null): View
    {
        $kodeCek = $kode ?? $request->query('kode');
        $hasil = null;

        if (!empty($kodeCek)) {
            $hasil = $this->ticketService->verifyTicket($kodeCek);
        }

        return view('tiket.verifikasi', compact('hasil', 'kodeCek'));
    }
}
