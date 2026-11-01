<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Pengaturan;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Tampilkan Halaman Pembayaran & Petunjuk Transfer Bank (F10-01, W-09, US-07).
     */
    public function show(Request $request, int $id): View|RedirectResponse
    {
        $pemesanan = Pemesanan::with(['jadwal.paket', 'customTrip', 'pembayaran', 'peserta'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $pengaturan = Pengaturan::getSettings();

        return view('wisatawan.pembayaran.show', compact('pemesanan', 'pengaturan'));
    }

    /**
     * Simpan Bukti Pembayaran dari Wisatawan (F10-01, BR-13, T-07).
     */
    public function store(Request $request, int $id): RedirectResponse
    {
        $pemesanan = Pemesanan::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'bukti_transfer' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ], [
            'nominal.required' => 'Nominal transfer wajib diisi.',
            'nominal.min' => 'Nominal transfer minimal Rp 1.',
            'tanggal_bayar.required' => 'Tanggal transfer wajib diisi.',
            'tanggal_bayar.before_or_equal' => 'Tanggal transfer tidak boleh di masa depan.',
            'bukti_transfer.required' => 'File bukti transfer wajib diunggah.',
            'bukti_transfer.max' => 'Ukuran file melebihi 5 MB. Pilih file lain lalu coba lagi.',
            'bukti_transfer.mimes' => 'Format file bukti transfer harus JPG, PNG, atau PDF.',
        ]);

        $this->paymentService->submitPayment(
            $pemesanan,
            (float) $request->input('nominal'),
            $request->input('tanggal_bayar'),
            $request->file('bukti_transfer')
        );

        return redirect()->route('pesanan-saya.show', $pemesanan->id)
            ->with('sukses', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }
}
