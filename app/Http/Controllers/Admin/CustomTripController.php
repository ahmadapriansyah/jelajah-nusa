<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTrip;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomTripController extends Controller
{
    /**
     * Daftar Pengajuan Custom Trip & Panel Penawaran (F3-06, F3-07, Frame 107:2281).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $query = CustomTrip::with(['user', 'kendaraan'])->orderBy('id', 'desc');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $customTripList = $query->paginate(15)->withQueryString();

        $selectedId = $request->query('selected', $customTripList->first()?->id);
        $selectedTrip = $selectedId
            ? CustomTrip::with(['user', 'kendaraan', 'pemesanan'])->find($selectedId)
            : null;

        $kendaraanList = Kendaraan::tersedia()->get();

        return view('admin.custom_trip.index', compact('customTripList', 'selectedTrip', 'kendaraanList', 'status'));
    }

    /**
     * Kirim Penawaran Biaya, Jadwal, Armada, dan Fasilitas ke Wisatawan (F3-07).
     */
    public function berikanPenawaran(Request $request, int $id): RedirectResponse
    {
        $customTrip = CustomTrip::findOrFail($id);

        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'fasilitas_penawaran' => 'required|string',
            'jadwal_penawaran' => 'required|date',
            'biaya_penawaran' => 'required|numeric|min:1',
        ], [
            'kendaraan_id.required' => 'Armada kendaraan wajib dipilih.',
            'fasilitas_penawaran.required' => 'Fasilitas penawaran wajib diisi.',
            'jadwal_penawaran.required' => 'Jadwal penawaran tanggal wajib diisi.',
            'biaya_penawaran.required' => 'Total biaya penawaran wajib diisi.',
        ]);

        $customTrip->update([
            'kendaraan_id' => $validated['kendaraan_id'],
            'fasilitas_penawaran' => $validated['fasilitas_penawaran'],
            'jadwal_penawaran' => $validated['jadwal_penawaran'],
            'biaya_penawaran' => $validated['biaya_penawaran'],
            'status' => 'ditawarkan',
        ]);

        LogAktivitas::catat(
            aksi: 'penawaran_custom_trip',
            subjekTipe: 'CustomTrip',
            subjekId: $customTrip->id,
            keterangan: "Admin mengirimkan penawaran seharga " . $customTrip->biaya_penawaran_format . " untuk Custom Trip tujuan {$customTrip->tujuan} ({$customTrip->user->nama_lengkap})."
        );

        return back()->with('sukses', 'Penawaran berhasil dikirimkan ke wisatawan.');
    }

    /**
     * Tolak pengajuan custom trip (F3-07).
     */
    public function tolak(Request $request, int $id): RedirectResponse
    {
        $customTrip = CustomTrip::findOrFail($id);

        $customTrip->update(['status' => 'ditolak']);

        LogAktivitas::catat(
            aksi: 'tolak_custom_trip',
            subjekTipe: 'CustomTrip',
            subjekId: $customTrip->id,
            keterangan: "Pengajuan Custom Trip tujuan {$customTrip->tujuan} ditolak oleh admin."
        );

        return back()->with('sukses', 'Pengajuan custom trip telah ditolak.');
    }
}
