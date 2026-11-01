<?php

namespace App\Services;

use App\Models\CustomTrip;
use App\Models\JadwalKeberangkatan;
use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Data untuk Admin Dashboard (22.1 & Frame 50:2).
     */
    public function getDashboardData(): array
    {
        $now = now();
        $startThisMonth = $now->copy()->startOfMonth();
        $endThisMonth = $now->copy()->endOfMonth();
        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth = $now->copy()->subMonth()->endOfMonth();

        // 1. Total Pemesanan (tidak termasuk dibatalkan)
        $totalPemesananBulanIni = Pemesanan::whereBetween('created_at', [$startThisMonth, $endThisMonth])
            ->where('status', '!=', 'dibatalkan')
            ->count();
        $totalPemesananBulanLalu = Pemesanan::whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->where('status', '!=', 'dibatalkan')
            ->count();
        $persenPemesanan = $this->hitungPersenPerubahan($totalPemesananBulanIni, $totalPemesananBulanLalu);

        // 2. Pendapatan Bulan Ini (pembayaran diverifikasi)
        $pendapatanBulanIni = (float) Pembayaran::where('status', 'diverifikasi')
            ->whereBetween('diverifikasi_pada', [$startThisMonth, $endThisMonth])
            ->sum('nominal');
        $pendapatanBulanLalu = (float) Pembayaran::where('status', 'diverifikasi')
            ->whereBetween('diverifikasi_pada', [$startLastMonth, $endLastMonth])
            ->sum('nominal');
        $persenPendapatan = $this->hitungPersenPerubahan($pendapatanBulanIni, $pendapatanBulanLalu);

        // 3. Menunggu Verifikasi
        $pembayaranMenungguQuery = Pembayaran::where('status', 'menunggu')->orderBy('created_at', 'asc');
        $jumlahMenunggu = $pembayaranMenungguQuery->count();
        $tertua = $pembayaranMenungguQuery->first();
        $umurTertuaHari = $tertua ? (int) $tertua->created_at->diffInDays(now()) : 0;

        // 4. Keberangkatan Mendatang (30 hari ke depan)
        $jadwalMendatang = JadwalKeberangkatan::with(['paket', 'kendaraan'])
            ->where('tanggal_berangkat', '>=', now()->toDateString())
            ->where('tanggal_berangkat', '<=', now()->addDays(30)->toDateString())
            ->whereIn('status_keberangkatan', ['dijadwalkan', 'dikonfirmasi'])
            ->get();

        $jumlahKeberangkatanMendatang = $jadwalMendatang->count();
        $belumMemenuhiMin = $jadwalMendatang->filter(fn($j) => $j->status_kelayakan === 'Belum memenuhi minimal')->count();

        // 5. Grafik Pendapatan 6 Bulan Terakhir
        $grafikPendapatan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bln = now()->copy()->subMonths($i);
            $start = $bln->copy()->startOfMonth();
            $end = $bln->copy()->endOfMonth();
            $totalBln = (float) Pembayaran::where('status', 'diverifikasi')
                ->whereBetween('diverifikasi_pada', [$start, $end])
                ->sum('nominal');

            $grafikPendapatan[] = [
                'bulan' => $bln->translatedFormat('M Y'),
                'total_jt' => round($totalBln / 1000000, 1),
                'nominal' => $totalBln,
            ];
        }

        // 6. Paket Terlaris (Top 4)
        $paketTerlaris = $this->getPaketTerlaris(now()->subMonths(6)->toDateString(), now()->toDateString(), 4);

        // 7. Panel Perlu Tindakan
        $customTripBaru = CustomTrip::with('user')
            ->where('status', 'diajukan')
            ->latest()
            ->take(5)
            ->get();

        $jadwalPerluTindakan = $jadwalMendatang->filter(fn($j) => $j->status_kelayakan === 'Belum memenuhi minimal')->values()->take(5);

        return [
            'total_pemesanan' => $totalPemesananBulanIni,
            'persen_pemesanan' => $persenPemesanan,
            'pendapatan_bulan_ini' => $pendapatanBulanIni,
            'pendapatan_bulan_ini_format' => $this->formatJutaRupiah($pendapatanBulanIni),
            'persen_pendapatan' => $persenPendapatan,
            'jumlah_menunggu' => $jumlahMenunggu,
            'umur_tertua_hari' => $umurTertuaHari,
            'keberangkatan_mendatang' => $jumlahKeberangkatanMendatang,
            'keberangkatan_belum_min' => $belumMemenuhiMin,
            'grafik_pendapatan' => $grafikPendapatan,
            'paket_terlaris' => $paketTerlaris,
            'custom_trip_baru' => $customTripBaru,
            'jadwal_perlu_tindakan' => $jadwalPerluTindakan,
        ];
    }

    /**
     * Hitung laporan berkala berdasarkan filter tanggal (F7-01, 22.11).
     */
    public function getLaporanPeriodik(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfMonth();

        $jadwalList = JadwalKeberangkatan::with(['paket', 'kendaraan'])
            ->whereBetween('tanggal_berangkat', [$start->toDateString(), $end->toDateString()])
            ->get();

        $dijalankan = 0;
        $ditunda = 0;
        $dibatalkan = 0;

        foreach ($jadwalList as $j) {
            if ($j->status_keberangkatan === 'ditunda') {
                $ditunda++;
            } elseif ($j->status_keberangkatan === 'dibatalkan') {
                $dibatalkan++;
            } else {
                // Berstatus dijadwalkan / dikonfirmasi
                if ($j->tanggal_berangkat->isPast() || $j->status_keberangkatan === 'dikonfirmasi') {
                    $dijalankan++;
                }
            }
        }

        // Total peserta dari pemesanan yang dibuat/berlangsung di periode ini yang tidak dibatalkan
        $totalPeserta = (int) Pemesanan::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'dibatalkan')
            ->sum('jumlah_peserta');

        // Total pendapatan
        $totalPendapatan = (float) Pembayaran::where('status', 'diverifikasi')
            ->whereBetween('diverifikasi_pada', [$start, $end])
            ->sum('nominal');

        $paketTerlaris = $this->getPaketTerlaris($start->toDateString(), $end->toDateString());

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'dijalankan' => $dijalankan,
            'ditunda' => $ditunda,
            'dibatalkan' => $dibatalkan,
            'total_peserta' => $totalPeserta,
            'total_pendapatan' => $totalPendapatan,
            'total_pendapatan_format' => 'Rp ' . number_format($totalPendapatan, 0, ',', '.'),
            'paket_terlaris' => $paketTerlaris,
        ];
    }

    /**
     * Ambil data paket terlaris berdasarkan peserta dan pendapatan.
     */
    public function getPaketTerlaris(string $startDate, string $endDate, int $limit = 10): array
    {
        $paketList = PaketWisata::all();
        $results = [];

        foreach ($paketList as $p) {
            // Jumlah peserta pada pesanan dikonfirmasi
            $pesananDikonfirmasi = Pemesanan::whereHas('jadwal', function ($q) use ($p) {
                $q->where('paket_wisata_id', $p->id);
            })
            ->where('status', 'dikonfirmasi')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

            $pesertaCount = $pesananDikonfirmasi->sum('jumlah_peserta');
            $pendapatan = $pesananDikonfirmasi->sum('total_biaya');

            if ($pesertaCount > 0 || $pendapatan > 0) {
                $results[] = [
                    'id' => $p->id,
                    'nama_paket' => $p->nama_paket,
                    'destinasi' => $p->destinasi,
                    'jenis_layanan' => $p->jenis_layanan === 'open_trip' ? 'Open Trip' : 'Paket Reguler',
                    'peserta' => (int) $pesertaCount,
                    'pendapatan' => (float) $pendapatan,
                    'pendapatan_format' => 'Rp ' . number_format($pendapatan, 0, ',', '.'),
                ];
            }
        }

        // Urutkan berdasarkan peserta terbanyak, lalu pendapatan
        usort($results, function ($a, $b) {
            if ($a['peserta'] === $b['peserta']) {
                return $b['pendapatan'] <=> $a['pendapatan'];
            }
            return $b['peserta'] <=> $a['peserta'];
        });

        // Beri ranking
        $ranked = [];
        $rank = 1;
        foreach (array_slice($results, 0, $limit) as $item) {
            $item['peringkat'] = $rank++;
            $ranked[] = $item;
        }

        return $ranked;
    }

    /**
     * Hitung persentase perubahan bulan ini vs bulan lalu (T-20).
     */
    private function hitungPersenPerubahan(float $current, float $previous): ?float
    {
        if ($previous == 0) {
            return null; // Tampil "—" di UI jika bulan lalu bernilai 0
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Format Rupiah singkat (e.g. "Rp 186,4 jt").
     */
    private function formatJutaRupiah(float $amount): string
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . ' M';
        }
        if ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . ' jt';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
