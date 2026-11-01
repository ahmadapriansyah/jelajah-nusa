<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKeberangkatan;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\PaketWisata;
use App\Services\QuotaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function __construct(
        protected QuotaService $quotaService
    ) {}

    /**
     * Tambah Jadwal Keberangkatan Baru untuk Paket Wisata (F2-04, BR-05, T-18).
     */
    public function store(Request $request, int $paketId): RedirectResponse
    {
        $paket = PaketWisata::findOrFail($paketId);

        $validated = $request->validate([
            'tanggal_berangkat' => 'required|date|after:today',
            'kuota' => 'required|integer|min:1',
            'min_peserta' => 'required|integer|min:1|lte:kuota',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',
            'biaya_operasional' => 'nullable|numeric|min:0',
        ], [
            'tanggal_berangkat.required' => 'Tanggal keberangkatan wajib diisi.',
            'tanggal_berangkat.after' => 'Tanggal keberangkatan harus setelah hari ini.',
            'kuota.required' => 'Kuota peserta wajib diisi.',
            'min_peserta.lte' => 'Minimal peserta tidak boleh melebihi kuota.',
        ]);

        // Validasi BR-05: Kuota tidak boleh melebihi kapasitas kendaraan
        if (!empty($validated['kendaraan_id'])) {
            $kendaraan = Kendaraan::findOrFail($validated['kendaraan_id']);
            if ($validated['kuota'] > $kendaraan->kapasitas) {
                return back()->withErrors([
                    'kuota' => "Kuota ({$validated['kuota']}) tidak boleh melebihi kapasitas armada kendaraan ({$kendaraan->kapasitas} kursi).",
                ])->withInput();
            }
        }

        $jadwal = JadwalKeberangkatan::create([
            'paket_wisata_id' => $paket->id,
            'kendaraan_id' => $validated['kendaraan_id'] ?? null,
            'tanggal_berangkat' => $validated['tanggal_berangkat'],
            'kuota' => $validated['kuota'],
            'sisa_kuota' => $validated['kuota'],
            'min_peserta' => $validated['min_peserta'],
            'biaya_operasional' => $validated['biaya_operasional'] ?? 0,
            'status_keberangkatan' => 'dijadwalkan',
        ]);

        LogAktivitas::catat(
            aksi: 'tambah_jadwal',
            subjekTipe: 'JadwalKeberangkatan',
            subjekId: $jadwal->id,
            keterangan: "Jadwal tanggal {$jadwal->tanggal_berangkat->format('d/m/Y')} ditambahkan untuk paket {$paket->nama_paket}."
        );

        return redirect()->route('admin.paket.index', ['selected' => $paket->id])
            ->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Ubah Jadwal Keberangkatan (BR-05, T-18).
     */
    public function update(Request $request, int $jadwalId): RedirectResponse
    {
        $jadwal = JadwalKeberangkatan::with(['pemesanan', 'paket'])->findOrFail($jadwalId);

        $validated = $request->validate([
            'tanggal_berangkat' => 'required|date',
            'kuota' => 'required|integer|min:1',
            'min_peserta' => 'required|integer|min:1|lte:kuota',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',
            'biaya_operasional' => 'nullable|numeric|min:0',
        ]);

        $pesertaAktif = $jadwal->peserta_aktif;

        // BR-05 & T-18: Kuota tidak boleh diturunkan di bawah jumlah peserta aktif
        if ($validated['kuota'] < $pesertaAktif) {
            return back()->withErrors([
                'kuota' => "Kuota tidak boleh diturunkan di bawah jumlah peserta yang sudah mendaftar ({$pesertaAktif} orang).",
            ]);
        }

        // BR-05: Kuota tidak boleh melebihi kapasitas kendaraan
        if (!empty($validated['kendaraan_id'])) {
            $kendaraan = Kendaraan::findOrFail($validated['kendaraan_id']);
            if ($validated['kuota'] > $kendaraan->kapasitas) {
                return back()->withErrors([
                    'kuota' => "Kuota ({$validated['kuota']}) tidak boleh melebihi kapasitas armada ({$kendaraan->kapasitas} kursi).",
                ]);
            }
        }

        $jadwal->update([
            'tanggal_berangkat' => $validated['tanggal_berangkat'],
            'kuota' => $validated['kuota'],
            'min_peserta' => $validated['min_peserta'],
            'kendaraan_id' => $validated['kendaraan_id'] ?? null,
            'biaya_operasional' => $validated['biaya_operasional'] ?? $jadwal->biaya_operasional,
        ]);

        $this->quotaService->syncRemainingSeats($jadwal);

        LogAktivitas::catat(
            aksi: 'ubah_jadwal',
            subjekTipe: 'JadwalKeberangkatan',
            subjekId: $jadwal->id,
            keterangan: "Jadwal ID #{$jadwal->id} diperbarui."
        );

        return redirect()->route('admin.paket.index', ['selected' => $jadwal->paket_wisata_id])
            ->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /**
     * Hapus Jadwal Keberangkatan (BR-12).
     */
    public function destroy(int $jadwalId): RedirectResponse
    {
        $jadwal = JadwalKeberangkatan::findOrFail($jadwalId);

        if ($jadwal->pemesanan()->exists()) {
            return back()->with('error', 'Data ini sudah dipakai pemesanan dan tidak bisa dihapus. Nonaktifkan saja.');
        }

        $paketId = $jadwal->paket_wisata_id;
        $jadwal->delete();

        return redirect()->route('admin.paket.index', ['selected' => $paketId])
            ->with('sukses', 'Perubahan berhasil disimpan.');
    }
}
