<?php

namespace App\Http\Controllers;

use App\Models\CustomTrip;
use App\Models\JadwalKeberangkatan;
use App\Models\Pemesanan;
use App\Services\BookingService;
use App\Services\QuotaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PemesananController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected QuotaService $quotaService
    ) {}

    /**
     * Tampilkan formulir pemesanan paket (F9-05, W-07, US-05).
     */
    public function create(Request $request): View|RedirectResponse
    {
        $jadwalId = $request->query('jadwal_id');
        $customTripId = $request->query('custom_trip_id');

        $jadwal = null;
        $customTrip = null;

        if ($jadwalId) {
            $jadwal = JadwalKeberangkatan::with(['paket', 'kendaraan'])->findOrFail($jadwalId);
            if ($jadwal->sisa_kuota <= 0) {
                return back()->with('error', 'Maaf, kuota untuk jadwal keberangkatan ini telah habis.');
            }
        } elseif ($customTripId) {
            $customTrip = CustomTrip::where('user_id', $request->user()->id)
                ->where('status', 'disetujui')
                ->findOrFail($customTripId);
        } else {
            return redirect()->route('paket.index')->with('error', 'Silakan pilih jadwal paket wisata terlebih dahulu.');
        }

        return view('wisatawan.pemesanan.create', compact('jadwal', 'customTrip'));
    }

    /**
     * Simpan pemesanan baru dan validasi kuota secara atomik (F9-05, F9-06, US-05, US-06, T-04, T-05).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'jadwal_id' => 'nullable|required_without:custom_trip_id|exists:jadwal_keberangkatan,id',
            'custom_trip_id' => 'nullable|required_without:jadwal_id|exists:custom_trip,id',
            'peserta' => 'required|array|min:1',
            'peserta.*.nama_lengkap' => 'required|string|max:100',
            'peserta.*.jenis_identitas' => 'required|in:ktp,paspor',
            'peserta.*.no_identitas' => 'required|string|max:30',
            'peserta.*.file_identitas' => 'required|file|mimes:jpeg,jpg,png|max:5120',
            'peserta.*.no_hp' => 'nullable|string|max:20',
        ], [
            'peserta.required' => 'Minimal harus ada 1 peserta dalam pemesanan.',
            'peserta.*.nama_lengkap.required' => 'Nama lengkap peserta wajib diisi.',
            'peserta.*.no_identitas.required' => 'Nomor identitas (KTP/Paspor) wajib diisi.',
            'peserta.*.file_identitas.required' => 'Berkas foto identitas (KTP/Paspor) wajib diunggah.',
            'peserta.*.file_identitas.max' => 'Ukuran berkas identitas melebihi 5 MB.',
            'peserta.*.file_identitas.mimes' => 'Format berkas identitas harus JPG atau PNG.',
        ]);

        $jadwalId = $request->input('jadwal_id') ? (int) $request->input('jadwal_id') : null;
        $customTripId = $request->input('custom_trip_id') ? (int) $request->input('custom_trip_id') : null;

        // Proses unggah berkas identitas peserta ke storage privat
        $pesertaData = [];
        foreach ($request->input('peserta') as $index => $data) {
            $file = $request->file("peserta.{$index}.file_identitas");
            $ext = $file->getClientOriginalExtension();
            $randomName = Str::random(32) . '.' . $ext;
            $path = $file->storeAs('private/identitas', $randomName);

            $pesertaData[] = [
                'nama_lengkap' => $data['nama_lengkap'],
                'jenis_identitas' => $data['jenis_identitas'],
                'no_identitas' => $data['no_identitas'],
                'file_identitas_path' => $path,
                'no_hp' => $data['no_hp'] ?? null,
            ];
        }

        // Jalankan create booking di service class dengan lockForUpdate
        $pemesanan = $this->bookingService->createBooking(
            $user,
            $jadwalId,
            $pesertaData,
            $customTripId
        );

        return redirect()->route('pembayaran.show', $pemesanan->id)
            ->with('sukses', "Pemesanan berhasil dibuat ({$pemesanan->display_kode}). Silakan lakukan pembayaran sebelum batas waktu.");
    }
}
