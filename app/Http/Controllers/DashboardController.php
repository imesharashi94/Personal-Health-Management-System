<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Report;
use App\Services\TrendService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected TrendService $trendService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // KPIs for steps, hr, and sleep
        $kpis = [
            'steps' => $this->trendService->getKpiSummary($user, 'steps'),
            'hr' => $this->trendService->getKpiSummary($user, 'hr'),
            'sleep' => $this->trendService->getKpiSummary($user, 'sleep'),
        ];

        // Charts data
        $charts = [
            'steps' => $this->trendService->getChartData($user, 'steps', 14),
            'hr' => $this->trendService->getChartData($user, 'hr', 14),
            'sleep' => $this->trendService->getChartData($user, 'sleep', 14),
        ];

        // Recent alerts
        $alerts = Alert::where('user_id', $user->id)
            ->whereNull('resolved_at')
            ->orderBy('triggered_at', 'desc')
            ->take(5)
            ->get();

        // Latest lab report summary
        $latestReport = Report::where('user_id', $user->id)
            ->where('status', 'parsed')
            ->with('observations')
            ->orderBy('report_date', 'desc')
            ->first();

        return Inertia::render('Dashboard', [
            'kpis' => $kpis,
            'charts' => $charts,
            'alerts' => $alerts,
            'latestReport' => $latestReport,
        ]);
    }
}

