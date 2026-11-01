<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Tampilkan Ringkasan Dashboard Operasional Admin (F7-03, Frame 50:2).
     */
    public function index(): View
    {
        $dashboardData = $this->reportService->getDashboardData();

        return view('admin.dashboard.index', $dashboardData);
    }
}
