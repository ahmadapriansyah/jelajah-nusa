<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKeberangkatan;
use App\Services\FeasibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelayakanController extends Controller
{
    public function __construct(
        protected FeasibilityService $feasibilityService
    ) {}

    /**
     * Halaman Kelayakan Keberangkatan (F5, Frame 107:2446, US-10, US-11).
     */
    public function index(Request $request): View
    {
        $jadwalList = JadwalKeberangkatan::with(['paket', 'kendaraan', 'pemesanan.peserta'])
            ->where('tanggal_berangkat', '>=', now()->toDateString())
            ->orderBy('tanggal_berangkat', 'asc')
            ->get();

        $evaluasiJadwal = $jadwalList->map(fn($j) => $this->feasibilityService->evaluate($j));

        $selectedId = $request->query('selected', $jadwalList->first()?->id);
        $selectedEvaluasi = $evaluasiJadwal->firstWhere('jadwal.id', (int) $selectedId) ?? $evaluasiJadwal->first();

        return view('admin.kelayakan.index', compact('evaluasiJadwal', 'selectedEvaluasi'));
    }

    /**
     * Simpan Keputusan Keberangkatan & Update Biaya Operasional (US-11, BR-09, T-12).
     */
    public function updateKeputusan(Request $request, int $id): RedirectResponse
    {
        $jadwal = JadwalKeberangkatan::with('paket')->findOrFail($id);

        $validated = $request->validate([
            'status_keberangkatan' => 'required|in:dijadwalkan,dikonfirmasi,ditunda,dibatalkan',
            'biaya_operasional' => 'nullable|numeric|min:0',
        ]);

        $this->feasibilityService->updateDecision(
            $jadwal,
            $validated['status_keberangkatan'],
            isset($validated['biaya_operasional']) ? (float) $validated['biaya_operasional'] : null,
            $request->user()
        );

        $statusPesan = ucfirst($validated['status_keberangkatan']);
        return back()->with('sukses', "Keputusan berhasil disimpan. Jadwal kini berstatus {$statusPesan}.");
    }
}
