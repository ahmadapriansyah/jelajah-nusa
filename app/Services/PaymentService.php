<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        protected TicketService $ticketService,
        protected QuotaService $quotaService
    ) {}

    /**
     * Simpan bukti pembayaran dari wisatawan (F10-01).
     */
    public function submitPayment(
        Pemesanan $pemesanan,
        float $nominal,
        string $tanggalBayar,
        UploadedFile $buktiFile
    ): Pembayaran {
        if ($pemesanan->status === 'dibatalkan') {
            throw ValidationException::withMessages([
                'pemesanan' => 'Pemesanan ini sudah dibatalkan.',
            ]);
        }

        if ($nominal <= 0) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal pembayaran harus lebih dari Rp 0.',
            ]);
        }

        if ($nominal > $pemesanan->sisa_tagihan) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal melebihi sisa tagihan (' . $pemesanan->sisa_tagihan_format . ').',
            ]);
        }

        // Simpan file ke private storage
        $extension = $buktiFile->getClientOriginalExtension();
        $randomName = Str::random(32) . '.' . $extension;
        $path = $buktiFile->storeAs('private/bukti_transfer', $randomName);

        $pembayaran = Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'nominal' => $nominal,
            'tanggal_bayar' => $tanggalBayar,
            'bukti_transfer' => $path,
            'status' => 'menunggu',
        ]);

        LogAktivitas::catat(
            aksi: 'unggah_pembayaran',
            subjekTipe: 'Pembayaran',
            subjekId: $pembayaran->id,
            keterangan: "Wisatawan mengunggah bukti pembayaran sebesar Rp " . number_format($nominal, 0, ',', '.') . " untuk pesanan {$pemesanan->kode_pemesanan}",
            userId: auth()->id() ?? $pemesanan->user_id
        );

        return $pembayaran;
    }

    /**
     * Verifikasi pembayaran oleh admin (F4-02, BR-06, T-10).
     */
    public function verifyPayment(Pembayaran $pembayaran, User $admin): void
    {
        DB::transaction(function () use ($pembayaran, $admin) {
            $pemesanan = $pembayaran->pemesanan()->lockForUpdate()->first();

            $pembayaran->update([
                'status' => 'diverifikasi',
                'diverifikasi_pada' => now(),
                'diverifikasi_oleh' => $admin->id,
            ]);

            // Cek apakah dengan verifikasi ini pesanan telah lunas
            $totalTerverifikasi = $pemesanan->pembayaran()
                ->where('status', 'diverifikasi')
                ->sum('nominal');

            if ($totalTerverifikasi >= (float) $pemesanan->total_biaya) {
                $pemesanan->update(['status' => 'dikonfirmasi']);

                // Terbitkan tiket otomatis untuk semua peserta (F6-01, BR-07)
                $this->ticketService->issueTickets($pemesanan);

                // Sinkronkan sisa kuota jika ada jadwal
                if ($pemesanan->jadwal) {
                    $this->quotaService->syncRemainingSeats($pemesanan->jadwal);
                }
            }

            LogAktivitas::catat(
                aksi: 'verifikasi_pembayaran',
                subjekTipe: 'Pembayaran',
                subjekId: $pembayaran->id,
                keterangan: "Pembayaran ID #{$pembayaran->id} diverifikasi oleh {$admin->nama_lengkap}. Status pesanan: {$pemesanan->status}",
                userId: $admin->id
            );
        });
    }

    /**
     * Tolak bukti pembayaran dengan catatan wajib (BR-06, US-09, T-09).
     */
    public function rejectPayment(Pembayaran $pembayaran, string $catatanAdmin, User $admin): void
    {
        $catatan = trim($catatanAdmin);
        if (empty($catatan)) {
            throw ValidationException::withMessages([
                'catatan_admin' => 'Alasan penolakan wajib diisi.',
            ]);
        }

        $pembayaran->update([
            'status' => 'ditolak',
            'catatan_admin' => $catatan,
            'diverifikasi_pada' => now(),
            'diverifikasi_oleh' => $admin->id,
        ]);

        LogAktivitas::catat(
            aksi: 'tolak_pembayaran',
            subjekTipe: 'Pembayaran',
            subjekId: $pembayaran->id,
            keterangan: "Pembayaran ID #{$pembayaran->id} ditolak oleh {$admin->nama_lengkap}. Alasan: {$catatan}",
            userId: $admin->id
        );
    }
}
