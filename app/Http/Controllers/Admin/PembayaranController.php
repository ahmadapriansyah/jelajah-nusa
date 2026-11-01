<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
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
     * Antrean Verifikasi Pembayaran (F4-01, F4-02, Frame 107:1806).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'menunggu');

        $query = Pembayaran::with(['pemesanan.user', 'pemesanan.jadwal.paket', 'pemesanan.customTrip'])
            ->orderBy('id', 'asc');

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $antrean = $query->get();

        $selectedId = $request->query('selected', $antrean->first()?->id);
        $selectedPembayaran = $selectedId
            ? Pembayaran::with(['pemesanan.user', 'pemesanan.jadwal.paket', 'pemesanan.customTrip', 'verifikator'])->find($selectedId)
            : null;

        return view('admin.pembayaran.index', compact('antrean', 'selectedPembayaran', 'status'));
    }

    /**
     * Verifikasi Pembayaran (F4-02, BR-06, T-10).
     */
    public function verifikasi(Request $request, int $id): RedirectResponse
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $this->paymentService->verifyPayment($pembayaran, $request->user());

        return redirect()->route('admin.pembayaran.index')
            ->with('sukses', 'Pembayaran diverifikasi. Tiket telah terbit.');
    }

    /**
     * Tolak Bukti Transfer (F4-02, Frame 107:1096, BR-06, US-09, T-09).
     */
    public function tolak(Request $request, int $id): RedirectResponse
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'catatan_admin' => 'required|string|min:5|max:500',
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
            'catatan_admin.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $this->paymentService->rejectPayment($pembayaran, $request->input('catatan_admin'), $request->user());

        return redirect()->route('admin.pembayaran.index')
            ->with('sukses', 'Pembayaran telah ditolak. Alasan berhasil dikirimkan ke wisatawan.');
    }
}
