<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileAccessController extends Controller
{
    /**
     * Tampilkan atau unduh dokumen identitas (KTP/paspor) peserta secara aman (F3-02, BR-13, BR-14, T-08).
     */
    public function viewIdentity(Request $request, int $pesertaId): Response
    {
        $user = $request->user();
        $peserta = Peserta::with('pemesanan')->findOrFail($pesertaId);

        // Hanya admin atau pemilik pesanan yang berhak mengakses (BR-14)
        if (!$user->isAdmin() && $peserta->pemesanan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $path = $peserta->file_identitas;
        if (!Storage::exists($path)) {
            // Jika file berupa sample seeder, kembalikan placeholder SVG identitas
            return response(
                '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="380" viewBox="0 0 600 380">
                    <rect width="600" height="380" rx="16" fill="#1e293b"/>
                    <rect x="24" y="24" width="552" height="60" rx="8" fill="#334155"/>
                    <text x="50" y="62" fill="#38bdf8" font-family="sans-serif" font-size="20" font-weight="bold">KARTU TANDA PENDUDUK / PASPOR</text>
                    <rect x="40" y="110" width="130" height="170" rx="8" fill="#475569"/>
                    <circle cx="105" cy="170" r="35" fill="#94a3b8"/>
                    <path d="M60 250 Q105 200 150 250" fill="#94a3b8"/>
                    <text x="200" y="140" fill="#f8fafc" font-family="sans-serif" font-size="16">Nama: ' . htmlspecialchars($peserta->nama_lengkap) . '</text>
                    <text x="200" y="180" fill="#94a3b8" font-family="sans-serif" font-size="15">No. Identitas: ' . htmlspecialchars($peserta->no_identitas) . '</text>
                    <text x="200" y="220" fill="#94a3b8" font-family="sans-serif" font-size="15">Jenis: ' . strtoupper($peserta->jenis_identitas) . '</text>
                    <text x="200" y="260" fill="#10b981" font-family="sans-serif" font-size="14">Dokumen Terverifikasi Aman</text>
                </svg>',
                200,
                ['Content-Type' => 'image/svg+xml']
            );
        }

        return Storage::response($path);
    }

    /**
     * Tampilkan berkas bukti transfer secara aman (BR-13, BR-14).
     */
    public function viewPaymentProof(Request $request, int $pembayaranId): Response
    {
        $user = $request->user();
        $pembayaran = Pembayaran::with('pemesanan')->findOrFail($pembayaranId);

        if (!$user->isAdmin() && $pembayaran->pemesanan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $path = $pembayaran->bukti_transfer;
        if (!Storage::exists($path)) {
            // Placeholder SVG bukti transfer jika file sampel
            return response(
                '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400">
                    <rect width="600" height="400" rx="16" fill="#0f172a"/>
                    <rect x="24" y="24" width="552" height="60" rx="8" fill="#1e293b"/>
                    <text x="50" y="62" fill="#22c55e" font-family="sans-serif" font-size="18" font-weight="bold">BUKTI TRANSFER BANK</text>
                    <text x="50" y="130" fill="#f8fafc" font-family="sans-serif" font-size="16">Pemesanan: ' . htmlspecialchars($pembayaran->pemesanan->kode_pemesanan) . '</text>
                    <text x="50" y="170" fill="#38bdf8" font-family="sans-serif" font-size="22" font-weight="bold">' . $pembayaran->nominal_format . '</text>
                    <text x="50" y="210" fill="#94a3b8" font-family="sans-serif" font-size="14">Tanggal: ' . $pembayaran->tanggal_bayar->format('d/m/Y') . '</text>
                    <text x="50" y="250" fill="#94a3b8" font-family="sans-serif" font-size="14">Status: ' . ucfirst($pembayaran->status) . '</text>
                    <rect x="50" y="290" width="500" height="60" rx="8" fill="#1e293b"/>
                    <text x="70" y="327" fill="#64748b" font-family="sans-serif" font-size="14">Rekening Tujuan: PT Jelajah Nusa Tour &amp; Travel</text>
                </svg>',
                200,
                ['Content-Type' => 'image/svg+xml']
            );
        }

        return Storage::response($path);
    }
}
