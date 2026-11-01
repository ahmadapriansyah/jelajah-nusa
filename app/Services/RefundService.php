<?php

namespace App\Services;

use App\Models\JadwalKeberangkatan;
use App\Models\LogAktivitas;
use App\Models\Pemesanan;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundService
{
    public function __construct(
        protected QuotaService $quotaService,
        protected TicketService $ticketService
    ) {}

    /**
     * Catat pengembalian dana (refund) (BR-10, US-12, T-19).
     */
    public function processRefund(
        Pemesanan $pemesanan,
        float $nominal,
        string $alasan,
        ?User $admin = null
    ): Pengembalian {
        $totalTerbayar = $pemesanan->total_terbayar;

        if ($nominal <= 0) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal pengembalian dana harus lebih besar dari Rp 0.',
            ]);
        }

        if ($nominal > $totalTerbayar) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal refund tidak boleh melebihi total yang telah dibayar (' . $pemesanan->total_terbayar_format . ').',
            ]);
        }

        return DB::transaction(function () use ($pemesanan, $nominal, $alasan, $admin) {
            // Catat pengembalian
            $pengembalian = Pengembalian::create([
                'pemesanan_id' => $pemesanan->id,
                'jadwal_baru_id' => null,
                'jenis' => 'refund',
                'nominal' => $nominal,
                'alasan' => $alasan,
                'tanggal_pengembalian' => now()->toDateString(),
            ]);

            // Batalkan pesanan dan tiket
            $pemesanan->update(['status' => 'dibatalkan']);
            $pemesanan->tiket()->update(['status' => 'dibatalkan']);

            // Kembalikan sisa kuota ke jadwal lama jika ada
            if ($pemesanan->jadwal_id && $pemesanan->jadwal) {
                $this->quotaService->syncRemainingSeats($pemesanan->jadwal);
            }

            LogAktivitas::catat(
                aksi: 'catat_refund',
                subjekTipe: 'Pengembalian',
                subjekId: $pengembalian->id,
                keterangan: "Refund sebesar Rp " . number_format($nominal, 0, ',', '.') . " dicatat untuk pesanan {$pemesanan->kode_pemesanan}. Alasan: {$alasan}",
                userId: $admin?->id ?? auth()->id()
            );

            return $pengembalian;
        });
    }

    /**
     * Catat pemindahan jadwal (reschedule) (BR-10, US-12).
     */
    public function processReschedule(
        Pemesanan $pemesanan,
        int $jadwalBaruId,
        string $alasan,
        ?User $admin = null
    ): Pengembalian {
        return DB::transaction(function () use ($pemesanan, $jadwalBaruId, $alasan, $admin) {
            $jadwalBaru = JadwalKeberangkatan::where('id', $jadwalBaruId)
                ->lockForUpdate()
                ->firstOrFail();

            // Validasi paket harus sama jika pemesanan berasal dari jadwal
            if ($pemesanan->jadwal && $jadwalBaru->paket_wisata_id !== $pemesanan->jadwal->paket_wisata_id) {
                throw ValidationException::withMessages([
                    'jadwal_baru_id' => 'Jadwal baru harus berasal dari paket wisata yang sama.',
                ]);
            }

            // Periksa sisa kuota jadwal baru
            $sisaBaru = $this->quotaService->getRemainingSeats($jadwalBaru);
            if ($pemesanan->jumlah_peserta > $sisaBaru) {
                throw ValidationException::withMessages([
                    'jadwal_baru_id' => "Kuota jadwal baru tidak mencukupi (sisa {$sisaBaru} kursi).",
                ]);
            }

            $jadwalLama = $pemesanan->jadwal;

            // Catat pengembalian jenis pindah_jadwal
            $pengembalian = Pengembalian::create([
                'pemesanan_id' => $pemesanan->id,
                'jadwal_baru_id' => $jadwalBaruId,
                'jenis' => 'pindah_jadwal',
                'nominal' => null,
                'alasan' => $alasan,
                'tanggal_pengembalian' => now()->toDateString(),
            ]);

            // Batalkan tiket lama
            $pemesanan->tiket()->update(['status' => 'dibatalkan']);

            // Pindahkan jadwal pemesanan
            $pemesanan->update(['jadwal_id' => $jadwalBaruId]);

            // Sinkronisasi kuota jadwal lama dan jadwal baru
            if ($jadwalLama) {
                $this->quotaService->syncRemainingSeats($jadwalLama);
            }
            $this->quotaService->syncRemainingSeats($jadwalBaru);

            // Jika pesanan sudah lunas/dikonfirmasi, terbitkan tiket baru untuk jadwal baru
            if ($pemesanan->status === 'dikonfirmasi') {
                $this->ticketService->issueTickets($pemesanan);
            }

            LogAktivitas::catat(
                aksi: 'pindah_jadwal',
                subjekTipe: 'Pengembalian',
                subjekId: $pengembalian->id,
                keterangan: "Pemesanan {$pemesanan->kode_pemesanan} dipindahkan ke jadwal tanggal {$jadwalBaru->tanggal_berangkat->format('d/m/Y')}. Alasan: {$alasan}",
                userId: $admin?->id ?? auth()->id()
            );

            return $pengembalian;
        });
    }
}
