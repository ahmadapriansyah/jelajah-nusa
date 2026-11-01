<?php

namespace App\Services;

use App\Models\Pemesanan;
use App\Models\Tiket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketService
{
    /**
     * Terbitkan tiket untuk seluruh peserta dalam pemesanan.
     * Kode unik berformat: JN-PSN{nomor}-{urutan 2 digit} (contoh: JN-PSN1042-01).
     */
    public function issueTickets(Pemesanan $pemesanan): array
    {
        $pemesanan->loadMissing(['peserta', 'jadwal.paket', 'customTrip']);

        $cleanKode = preg_replace('/[^0-9]/', '', $pemesanan->kode_pemesanan);
        if (empty($cleanKode)) {
            $cleanKode = str_pad((string) $pemesanan->id, 4, '0', STR_PAD_LEFT);
        }

        $issued = [];
        $index = 1;

        foreach ($pemesanan->peserta as $peserta) {
            $kodeTiket = sprintf('JN-PSN%s-%02d', $cleanKode, $index);

            // Cek apakah tiket sudah ada
            $tiket = Tiket::firstOrNew([
                'pemesanan_id' => $pemesanan->id,
                'peserta_id' => $peserta->id,
            ]);

            $tiket->kode_tiket = $kodeTiket;
            $tiket->tanggal_terbit = now();
            $tiket->status = 'aktif';

            // Generate QR Code data (tautan verifikasi publik)
            $verifyUrl = \Illuminate\Support\Facades\Route::has('tiket.verifikasi.kode') 
                ? route('tiket.verifikasi.kode', ['kode' => $kodeTiket]) 
                : url('/verifikasi/' . $kodeTiket);
            try {
                $svgQr = QrCode::size(200)->generate($verifyUrl);
                $tiket->qr_code = (string) $svgQr;
            } catch (\Throwable $e) {
                $tiket->qr_code = null;
            }

            $tiket->save();
            $issued[] = $tiket;
            $index++;
        }

        return $issued;
    }

    /**
     * Verifikasi keaslian tiket berdasarkan kode tiket.
     */
    public function verifyTicket(string $kode): array
    {
        $kodeClean = trim($kode);
        $tiket = Tiket::with(['peserta', 'pemesanan.user', 'pemesanan.jadwal.paket', 'pemesanan.customTrip'])
            ->where('kode_tiket', $kodeClean)
            ->first();

        if (!$tiket) {
            return [
                'valid' => false,
                'status_teks' => 'TIKET TIDAK VALID',
                'pesan' => 'Kode tiket tidak ditemukan di sistem.',
                'tiket' => null,
            ];
        }

        if ($tiket->status !== 'aktif') {
            return [
                'valid' => false,
                'status_teks' => 'TIKET TIDAK VALID',
                'pesan' => 'Tiket ini telah dibatalkan atau tidak aktif.',
                'tiket' => $tiket,
            ];
        }

        $pemesanan = $tiket->pemesanan;
        $namaPaket = $pemesanan->judul_trip;
        $tanggal = $pemesanan->tanggal_berangkat ? $pemesanan->tanggal_berangkat->translatedFormat('d F Y') : '—';

        return [
            'valid' => true,
            'status_teks' => 'TIKET VALID',
            'pesan' => 'Tiket terverifikasi asli dan aktif.',
            'tiket' => $tiket,
            'nama_peserta' => $tiket->peserta->nama_lengkap,
            'no_identitas' => $tiket->peserta->no_identitas,
            'nama_paket' => $namaPaket,
            'tanggal_berangkat' => $tanggal,
            'pemesan' => $pemesanan->user->nama_lengkap ?? '—',
        ];
    }
}
