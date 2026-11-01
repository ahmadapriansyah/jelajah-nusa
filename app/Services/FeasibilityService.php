<?php

namespace App\Services;

use App\Models\JadwalKeberangkatan;
use App\Models\LogAktivitas;
use App\Models\User;

class FeasibilityService
{
    /**
     * Hitung metrik kelayakan jadwal.
     */
    public function evaluate(JadwalKeberangkatan $jadwal): array
    {
        $jadwal->loadMissing(['paket', 'kendaraan', 'pemesanan.peserta']);

        $kuota = (int) $jadwal->kuota;
        $minPeserta = (int) $jadwal->min_peserta;
        $pesertaAktif = $jadwal->peserta_aktif;
        $pesertaDikonfirmasi = $jadwal->peserta_dikonfirmasi;
        $harga = $jadwal->paket ? (float) $jadwal->paket->harga : 0;
        $estimasiPendapatan = $pesertaDikonfirmasi * $harga;
        $biayaOperasional = (float) $jadwal->biaya_operasional;

        $persenKapasitas = $kuota > 0 ? min(100, round(($pesertaAktif / $kuota) * 100)) : 0;
        $persenMin = $kuota > 0 ? min(100, round(($minPeserta / $kuota) * 100)) : 0;

        $status = $jadwal->status_kelayakan;

        return [
            'jadwal' => $jadwal,
            'kuota' => $kuota,
            'min_peserta' => $minPeserta,
            'peserta_aktif' => $pesertaAktif,
            'peserta_dikonfirmasi' => $pesertaDikonfirmasi,
            'persen_kapasitas' => $persenKapasitas,
            'persen_min' => $persenMin,
            'biaya_operasional' => $biayaOperasional,
            'estimasi_pendapatan' => $estimasiPendapatan,
            'untung_rugi' => $estimasiPendapatan - $biayaOperasional,
            'status' => $status,
            'is_layak' => $status === 'Layak dijalankan' || $status === 'Penuh',
        ];
    }

    /**
     * Perbarui keputusan admin mengenai jadwal keberangkatan (BR-09, US-11).
     */
    public function updateDecision(
        JadwalKeberangkatan $jadwal,
        string $statusKeberangkatan,
        ?float $biayaOperasional = null,
        ?User $admin = null
    ): void {
        $updateData = ['status_keberangkatan' => $statusKeberangkatan];
        if ($biayaOperasional !== null) {
            $updateData['biaya_operasional'] = $biayaOperasional;
        }

        $jadwal->update($updateData);

        LogAktivitas::catat(
            aksi: 'keputusan_keberangkatan',
            subjekTipe: 'JadwalKeberangkatan',
            subjekId: $jadwal->id,
            keterangan: "Status keberangkatan jadwal ID #{$jadwal->id} ({$jadwal->paket->nama_paket} tgl {$jadwal->tanggal_berangkat->format('d/m/Y')}) diubah menjadi {$statusKeberangkatan}",
            userId: $admin?->id ?? auth()->id()
        );
    }
}
