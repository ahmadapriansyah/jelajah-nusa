<?php

namespace App\Http\Controllers;

use App\Models\CustomTrip;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomTripController extends Controller
{
    /**
     * Daftar Pengajuan Custom Trip Wisatawan.
     */
    public function index(Request $request): View
    {
        $customTrips = CustomTrip::with(['kendaraan', 'pemesanan'])
            ->where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('wisatawan.custom_trip.index', compact('customTrips'));
    }

    /**
     * Formulir Pengajuan Custom Trip (F9-07, W-12, US-13).
     */
    public function create(): View
    {
        return view('wisatawan.custom_trip.create');
    }

    /**
     * Simpan Pengajuan Custom Trip (US-13, T-16).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'tujuan' => 'required|string|max:150',
            'tanggal_diinginkan' => 'required|date|after:today',
            'durasi_hari' => 'required|integer|min:1|max:30',
            'jumlah_peserta' => 'required|integer|min:1|max:500',
            'kebutuhan' => 'required|string',
        ], [
            'tujuan.required' => 'Destinasi atau tujuan trip wajib diisi.',
            'tanggal_diinginkan.required' => 'Tanggal rencana berangkat wajib diisi.',
            'tanggal_diinginkan.after' => 'Tanggal rencana berangkat harus setelah hari ini.',
            'durasi_hari.required' => 'Durasi perjalanan (hari) wajib diisi.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'kebutuhan.required' => 'Deskripsi kebutuhan dan fasilitas wajib diisi.',
        ]);

        $customTrip = CustomTrip::create([
            'user_id' => $user->id,
            'tujuan' => $validated['tujuan'],
            'tanggal_diinginkan' => $validated['tanggal_diinginkan'],
            'durasi_hari' => $validated['durasi_hari'],
            'jumlah_peserta' => $validated['jumlah_peserta'],
            'kebutuhan' => $validated['kebutuhan'],
            'status' => 'diajukan',
        ]);

        LogAktivitas::catat(
            aksi: 'ajukan_custom_trip',
            subjekTipe: 'CustomTrip',
            subjekId: $customTrip->id,
            keterangan: "Wisatawan {$user->nama_lengkap} mengajukan Custom Trip ke {$customTrip->tujuan} untuk {$customTrip->jumlah_peserta} orang.",
            userId: $user->id
        );

        return redirect()->route('custom-trip.show', $customTrip->id)
            ->with('sukses', 'Pengajuan Custom Trip berhasil dikirimkan. Tim Jelajah Nusa akan segera menyusun penawaran terbaik untuk Anda.');
    }

    /**
     * Lihat Penawaran Custom Trip dari Admin (W-13, US-13).
     */
    public function show(Request $request, int $id): View
    {
        $customTrip = CustomTrip::with(['kendaraan', 'pemesanan'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return view('wisatawan.custom_trip.show', compact('customTrip'));
    }

    /**
     * Wisatawan Menyetujui Penawaran Custom Trip (BR-11, US-13, T-16).
     */
    public function setujui(Request $request, int $id): RedirectResponse
    {
        $customTrip = CustomTrip::where('user_id', $request->user()->id)
            ->where('status', 'ditawarkan')
            ->findOrFail($id);

        $customTrip->update(['status' => 'disetujui']);

        LogAktivitas::catat(
            aksi: 'setujui_penawaran_custom_trip',
            subjekTipe: 'CustomTrip',
            subjekId: $customTrip->id,
            keterangan: "Wisatawan menyetujui penawaran Custom Trip tujuan {$customTrip->tujuan}.",
            userId: $request->user()->id
        );

        return redirect()->route('pemesanan.create', ['custom_trip_id' => $customTrip->id])
            ->with('sukses', 'Penawaran disetujui! Silakan isi data peserta untuk menyelesaikan pemesanan Anda.');
    }

    /**
     * Wisatawan Menolak Penawaran Custom Trip.
     */
    public function tolak(Request $request, int $id): RedirectResponse
    {
        $customTrip = CustomTrip::where('user_id', $request->user()->id)
            ->where('status', 'ditawarkan')
            ->findOrFail($id);

        $customTrip->update(['status' => 'ditolak']);

        LogAktivitas::catat(
            aksi: 'tolak_penawaran_custom_trip',
            subjekTipe: 'CustomTrip',
            subjekId: $customTrip->id,
            keterangan: "Wisatawan menolak penawaran Custom Trip tujuan {$customTrip->tujuan}.",
            userId: $request->user()->id
        );

        return back()->with('sukses', 'Penawaran telah ditolak.');
    }
}
