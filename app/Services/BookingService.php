<?php

namespace App\Services;

use App\Models\CustomTrip;
use App\Models\JadwalKeberangkatan;
use App\Models\LogAktivitas;
use App\Models\Pemesanan;
use App\Models\Pengaturan;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        protected QuotaService $quotaService,
        protected TicketService $ticketService
    ) {}

    /**
     * Buat pemesanan baru dengan lockForUpdate untuk mencegah overbooking (BR-03, T-05).
     */
    public function createBooking(
        User $user,
        ?int $jadwalId,
        array $pesertaData,
        ?int $customTripId = null
    ): Pemesanan {
        if (!$jadwalId && !$customTripId) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'Pemesanan harus memiliki salah satu dari jadwal atau custom trip.',
            ]);
        }

        $jumlahPeserta = count($pesertaData);
        if ($jumlahPeserta < 1) {
            throw ValidationException::withMessages([
                'peserta' => 'Minimal harus ada 1 peserta.',
            ]);
        }

        return DB::transaction(function () use ($user, $jadwalId, $pesertaData, $customTripId, $jumlahPeserta) {
            $totalBiaya = 0;
            $jadwal = null;
            $customTrip = null;

            if ($jadwalId) {
                // Lock baris jadwal keberangkatan untuk race condition prevention
                $jadwal = JadwalKeberangkatan::where('id', $jadwalId)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Hitung sisa kuota yang akurat saat ini
                $sisaKuota = $this->quotaService->getRemainingSeats($jadwal);

                if ($jumlahPeserta > $sisaKuota) {
                    throw ValidationException::withMessages([
                        'jumlah_peserta' => "Kuota penuh — sisa {$sisaKuota} kursi, kurangi jumlah peserta.",
                    ]);
                }

                $paket = $jadwal->paket;
                if (!$paket || !$paket->status_aktif) {
                    throw ValidationException::withMessages([
                        'jadwal_id' => 'Paket wisata ini sedang tidak aktif.',
                    ]);
                }

                $totalBiaya = (float) $paket->harga * $jumlahPeserta;
            } elseif ($customTripId) {
                $customTrip = CustomTrip::where('id', $customTripId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($customTrip->user_id !== $user->id && !$user->isAdmin()) {
                    throw ValidationException::withMessages([
                        'custom_trip_id' => 'Anda tidak memiliki hak akses ke custom trip ini.',
                    ]);
                }

                if ($customTrip->status !== 'disetujui') {
                    throw ValidationException::withMessages([
                        'custom_trip_id' => 'Pemesanan hanya dapat dibuat dari custom trip yang telah disetujui.',
                    ]);
                }

                if ($customTrip->pemesanan()->exists()) {
                    throw ValidationException::withMessages([
                        'custom_trip_id' => 'Custom trip ini sudah pernah dipesan sebelumnya.',
                    ]);
                }

                $totalBiaya = (float) $customTrip->biaya_penawaran;
            }

            // Generate Kode Pemesanan unik berformat PSN-XXXX (contoh: PSN-1042)
            $countTotal = Pemesanan::count() + 1001;
            $kodePemesanan = 'PSN-' . $countTotal;
            while (Pemesanan::where('kode_pemesanan', $kodePemesanan)->exists()) {
                $countTotal++;
                $kodePemesanan = 'PSN-' . $countTotal;
            }

            $pengaturan = Pengaturan::getSettings();
            $batasJam = $pengaturan->batas_bayar_jam ?? 24;

            $pemesanan = Pemesanan::create([
                'kode_pemesanan' => $kodePemesanan,
                'user_id' => $user->id,
                'jadwal_id' => $jadwalId,
                'custom_trip_id' => $customTripId,
                'jumlah_peserta' => $jumlahPeserta,
                'total_biaya' => $totalBiaya,
                'batas_bayar' => now()->addHours($batasJam),
                'status' => 'menunggu',
            ]);

            // Simpan data peserta dan berkas identitas di storage privat
            foreach ($pesertaData as $data) {
                $filePath = $data['file_identitas_path']; // Sudah diunggah ke private storage oleh FormRequest/Controller

                Peserta::create([
                    'pemesanan_id' => $pemesanan->id,
                    'nama_lengkap' => $data['nama_lengkap'],
                    'jenis_identitas' => $data['jenis_identitas'],
                    'no_identitas' => $data['no_identitas'],
                    'file_identitas' => $filePath,
                    'no_hp' => $data['no_hp'] ?? null,
                ]);
            }

            // Kurangi sisa kuota (kursi ditahan saat pesanan dibuat, Bagian 19)
            if ($jadwal) {
                $this->quotaService->syncRemainingSeats($jadwal);
            }

            LogAktivitas::catat(
                aksi: 'buat_pemesanan',
                subjekTipe: 'Pemesanan',
                subjekId: $pemesanan->id,
                keterangan: "Pemesanan {$kodePemesanan} dibuat untuk {$jumlahPeserta} peserta oleh {$user->nama_lengkap}",
                userId: $user->id
            );

            return $pemesanan;
        });
    }

    /**
     * Batalkan pemesanan dan kembalikan kursi yang ditahan ke sisa kuota (BR-04, T-06, T-22).
     */
    public function cancelBooking(Pemesanan $pemesanan, string $alasan = 'Dibatalkan'): void
    {
        DB::transaction(function () use ($pemesanan, $alasan) {
            $oldStatus = $pemesanan->status;
            if ($oldStatus === 'dibatalkan') {
                return;
            }

            $pemesanan->update(['status' => 'dibatalkan']);

            // Nonaktifkan tiket jika sebelumnya ada tiket terbit
            $pemesanan->tiket()->update(['status' => 'dibatalkan']);

            // Kembalikan kuota jika terkait jadwal
            if ($pemesanan->jadwal_id && $pemesanan->jadwal) {
                $jadwal = JadwalKeberangkatan::where('id', $pemesanan->jadwal_id)->lockForUpdate()->first();
                if ($jadwal) {
                    $this->quotaService->syncRemainingSeats($jadwal);
                }
            }

            LogAktivitas::catat(
                aksi: 'batal_pemesanan',
                subjekTipe: 'Pemesanan',
                subjekId: $pemesanan->id,
                keterangan: "Pemesanan {$pemesanan->kode_pemesanan} dibatalkan. Alasan: {$alasan}",
                userId: auth()->id() ?? $pemesanan->user_id
            );
        });
    }

    /**
     * Batalkan otomatis seluruh pesanan berstatus menunggu yang melewati batas bayar (F12-02, T-21).
     */
    public function cancelExpiredBookings(): int
    {
        $expiredList = Pemesanan::where('status', 'menunggu')
            ->whereNotNull('batas_bayar')
            ->where('batas_bayar', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredList as $pesanan) {
            $this->cancelBooking($pesanan, 'Batas waktu pembayaran telah habis (24 jam).');
            $count++;
        }

        return $count;
    }
}
