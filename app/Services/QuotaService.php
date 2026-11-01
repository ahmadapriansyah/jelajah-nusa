<?php

namespace App\Services;

use App\Models\JadwalKeberangkatan;

class QuotaService
{
    /**
     * Hitung sisa kuota yang akurat berdasarkan peserta aktif (menunggu & dikonfirmasi).
     */
    public function getRemainingSeats(JadwalKeberangkatan $jadwal): int
    {
        $pesertaAktif = $jadwal->pemesanan()
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->sum('jumlah_peserta');

        return max(0, (int) $jadwal->kuota - (int) $pesertaAktif);
    }

    /**
     * Sinkronisasikan kolom sisa_kuota di database dengan hitungan nyata.
     */
    public function syncRemainingSeats(JadwalKeberangkatan $jadwal): int
    {
        $sisa = $this->getRemainingSeats($jadwal);
        $jadwal->update(['sisa_kuota' => $sisa]);
        return $sisa;
    }

    /**
     * Periksa apakah kuota cukup untuk jumlah peserta yang diminta.
     */
    public function checkAvailability(JadwalKeberangkatan $jadwal, int $jumlahPeserta): bool
    {
        return $this->getRemainingSeats($jadwal) >= $jumlahPeserta;
    }
}
