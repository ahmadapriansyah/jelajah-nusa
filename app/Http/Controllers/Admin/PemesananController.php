<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Pemesanan;
use App\Services\BookingService;
use App\Services\QuotaService;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PemesananController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected QuotaService $quotaService,
        protected TicketService $ticketService
    ) {}

    /**
     * Daftar Pemesanan Masuk & Panel Peserta (F3-01 sampai F3-05, Frame 107:2128).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('cari');

        $query = Pemesanan::with(['user', 'jadwal.paket', 'customTrip', 'peserta', 'pembayaran'])
            ->orderBy('id', 'desc');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $cleanSearch = ltrim($search, '#');
            $query->where(function ($q) use ($cleanSearch) {
                $q->where('kode_pemesanan', 'like', "%{$cleanSearch}%")
                  ->orWhereHas('user', function ($uq) use ($cleanSearch) {
                      $uq->where('nama_lengkap', 'like', "%{$cleanSearch}%");
                  })
                  ->orWhereHas('jadwal.paket', function ($pq) use ($cleanSearch) {
                      $pq->where('nama_paket', 'like', "%{$cleanSearch}%");
                  });
            });
        }

        $pemesananList = $query->paginate(15)->withQueryString();

        // Selected booking for participant document panel
        $selectedId = $request->query('selected', $pemesananList->first()?->id);
        $selectedPemesanan = $selectedId
            ? Pemesanan::with(['user', 'jadwal.paket', 'customTrip', 'peserta.tiket', 'pembayaran'])->find($selectedId)
            : null;

        return view('admin.pemesanan.index', compact('pemesananList', 'selectedPemesanan', 'status', 'search'));
    }

    /**
     * Ubah status pemesanan (F3-03, Bagian 21).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $pemesanan = Pemesanan::with(['jadwal', 'tiket'])->findOrFail($id);

        $request->validate([
            'status' => 'required|in:menunggu,dikonfirmasi,dibatalkan',
            'alasan' => 'nullable|string',
        ]);

        $newStatus = $request->input('status');
        $oldStatus = $pemesanan->status;

        if ($newStatus === $oldStatus) {
            return back();
        }

        if ($newStatus === 'dibatalkan') {
            $this->bookingService->cancelBooking($pemesanan, $request->input('alasan') ?? 'Dibatalkan oleh Admin Travel');
        } elseif ($newStatus === 'dikonfirmasi') {
            $pemesanan->update(['status' => 'dikonfirmasi']);
            $this->ticketService->issueTickets($pemesanan);
            if ($pemesanan->jadwal) {
                $this->quotaService->syncRemainingSeats($pemesanan->jadwal);
            }
        } else {
            $pemesanan->update(['status' => 'menunggu']);
        }

        LogAktivitas::catat(
            aksi: 'ubah_status_pemesanan',
            subjekTipe: 'Pemesanan',
            subjekId: $pemesanan->id,
            keterangan: "Status pemesanan {$pemesanan->kode_pemesanan} diubah dari {$oldStatus} menjadi {$newStatus}."
        );

        return back()->with('sukses', 'Perubahan berhasil disimpan.');
    }
}
