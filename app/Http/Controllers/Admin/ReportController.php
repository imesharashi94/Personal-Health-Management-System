<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\HealthMetric;
use App\Models\LabReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index(Request $request)
    {
        // KPIs
        $totalUsers = User::count();
        $activeUsers = User::whereHas('healthMetrics', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        })->count();

        $totalRecords = HealthMetric::count() + LabReport::count();
        
        $recentAlerts = Alert::where('triggered_at', '>=', Carbon::now()->subDays(7))
            ->whereNull('resolved_at')
            ->count();

        // Alert threshold configuration (could be moved to database)
        $alertThresholds = [
            'ldl_threshold' => 160,
            'hr_elevation' => 20,
            'sleep_minimum' => 5,
            'symptom_severity' => 6,
        ];

        return Inertia::render('Admin/Reports', [
            'kpis' => [
                'total_users' => $totalUsers,
                'active_users_30d' => $activeUsers,
                'total_records' => $totalRecords,
                'alerts_7d' => $recentAlerts,
            ],
            'alertThresholds' => $alertThresholds,
        ]);
    }
}

