<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananSayaController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Daftar Riwayat Pesanan Wisatawan (F10-02, F10-03, W-10).
     */
    public function index(Request $request): View
    {
        $pesananList = Pemesanan::with(['jadwal.paket', 'customTrip', 'peserta', 'pembayaran'])
            ->where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('wisatawan.pesanan.index', compact('pesananList'));
    }

    /**
     * Detail Pesanan, Tiket, & Status Pembayaran (F10-02, F10-04, W-11).
     */
    public function show(Request $request, int $id): View
    {
        $pemesanan = Pemesanan::with([
            'jadwal.paket',
            'jadwal.kendaraan',
            'customTrip.kendaraan',
            'peserta.tiket',
            'pembayaran' => fn($q) => $q->orderBy('id', 'desc'),
            'tiket',
            'refund',
        ])
        ->where('user_id', $request->user()->id)
        ->findOrFail($id);

        return view('wisatawan.pesanan.show', compact('pemesanan'));
    }

    /**
     * Wisatawan Membatalkan Pesanan Sendiri (F11-04, US-16, T-22).
     */
    public function batal(Request $request, int $id): RedirectResponse
    {
        $pemesanan = Pemesanan::where('user_id', $request->user()->id)->findOrFail($id);

        if ($pemesanan->status !== 'menunggu') {
            return back()->with('error', 'Pesanan yang telah dikonfirmasi atau dibatalkan tidak dapat dibatalkan mandiri.');
        }

        $this->bookingService->cancelBooking($pemesanan, 'Dibatalkan oleh wisatawan.');

        return back()->with('sukses', 'Pesanan Anda telah berhasil dibatalkan. Kuota telah dikembalikan.');
    }

    /**
     * Wisatawan Mengajukan Refund Pengembalian Dana.
     */
    public function ajukanRefund(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nama_bank' => 'required|string',
            'nomor_rekening' => 'required|string',
            'nama_pemilik' => 'required|string',
        ]);

        $pemesanan = Pemesanan::where('user_id', $request->user()->id)->findOrFail($id);

        if ($pemesanan->refund) {
            return back()->with('error', 'Pengajuan refund untuk pesanan ini sudah pernah dikirim.');
        }

        $pemesanan->refund()->create([
            'alasan' => 'Pengajuan refund oleh wisatawan (Trip Dibatalkan)',
            'metode_pembayaran' => $request->nama_bank,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_pemilik' => $request->nama_pemilik,
            'tanggal_pengembalian' => now(),
            'status' => 'pending',
        ]);

        return back()->with('sukses', 'Pengajuan refund berhasil dikirim. Mohon tunggu proses konfirmasi dari admin.');
    }
}