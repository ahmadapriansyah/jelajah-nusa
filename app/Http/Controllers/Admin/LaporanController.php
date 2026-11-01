<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Halaman Laporan Perjalanan & Pendapatan (F7, Frame 107:2817, US-15).
     */
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());

        $laporan = $this->reportService->getLaporanPeriodik($startDate, $endDate);

        return view('admin.laporan.index', compact('laporan', 'startDate', 'endDate'));
    }

    /**
     * Ekspor Laporan ke format PDF (F7-04, T-15).
     */
    public function eksporPdf(Request $request): Response
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());

        $laporan = $this->reportService->getLaporanPeriodik($startDate, $endDate);

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('laporan', 'startDate', 'endDate'))
            ->setPaper('a4', 'landscape');

        $filename = "Laporan_JelajahNusa_{$startDate}_{$endDate}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Ekspor Laporan ke format Excel / CSV (F7-04, T-15).
     */
    public function eksporExcel(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());

        $laporan = $this->reportService->getLaporanPeriodik($startDate, $endDate);

        $filename = "Laporan_JelajahNusa_{$startDate}_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($laporan, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['LAPORAN OPERASIONAL & PENDAPATAN JELAJAH NUSA']);
            fputcsv($handle, ['Periode', $startDate . ' s/d ' . $endDate]);
            fputcsv($handle, []);
            fputcsv($handle, ['RINGKASAN KEBERANGKATAN']);
            fputcsv($handle, ['Dijalankan', $laporan['dijalankan']]);
            fputcsv($handle, ['Ditunda', $laporan['ditunda']]);
            fputcsv($handle, ['Dibatalkan', $laporan['dibatalkan']]);
            fputcsv($handle, ['Total Peserta', $laporan['total_peserta']]);
            fputcsv($handle, ['Total Pendapatan', $laporan['total_pendapatan']]);
            fputcsv($handle, []);
            fputcsv($handle, ['PERINGKAT PAKET TERLARIS']);
            fputcsv($handle, ['Peringkat', 'Nama Paket', 'Destinasi', 'Jenis Layanan', 'Jumlah Peserta', 'Total Pendapatan (Rp)']);

            foreach ($laporan['paket_terlaris'] as $p) {
                fputcsv($handle, [
                    $p['peringkat'],
                    $p['nama_paket'],
                    $p['destinasi'],
                    $p['jenis_layanan'],
                    $p['peserta'],
                    $p['pendapatan'],
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
