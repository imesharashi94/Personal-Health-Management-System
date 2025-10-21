<?php

namespace App\Services;

use App\Models\HealthMetric;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrendService
{
    /**
     * Calculate moving average for a metric type.
     */
    public function movingAverage(User $user, string $metricType, int $days = 7): float
    {
        $startDate = Carbon::now()->subDays($days);

        $avg = HealthMetric::where('user_id', $user->id)
            ->where('metric_type', $metricType)
            ->where('date', '>=', $startDate)
            ->avg('value');

        return round((float) $avg, 2);
    }

    /**
     * Calculate baseline (median) for resting heart rate.
     */
    public function calculateBaseline(User $user, string $metricType = 'hr', int $days = 30): float
    {
        $startDate = Carbon::now()->subDays($days);

        $values = HealthMetric::where('user_id', $user->id)
            ->where('metric_type', $metricType)
            ->where('date', '>=', $startDate)
            ->pluck('value')
            ->sort()
            ->values();

        if ($values->isEmpty()) {
            return 0;
        }

        $count = $values->count();
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return $values[$middle];
    }

    /**
     * Get chart data for a metric type.
     */
    public function getChartData(User $user, string $metricType, int $days = 14): array
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $metrics = HealthMetric::where('user_id', $user->id)
            ->where('metric_type', $metricType)
            ->where('date', '>=', $startDate)
            ->orderBy('date', 'asc')
            ->get(['date', 'value']);

        return $metrics->map(function ($metric) {
            return [
                'date' => $metric->date->format('M d'),
                'value' => (float) $metric->value,
            ];
        })->toArray();
    }

    /**
     * Get KPI summary for multiple time periods.
     */
    public function getKpiSummary(User $user, string $metricType): array
    {
        return [
            '7d' => $this->movingAverage($user, $metricType, 7),
            '30d' => $this->movingAverage($user, $metricType, 30),
            '90d' => $this->movingAverage($user, $metricType, 90),
        ];
    }

    /**
     * Detect consecutive days above threshold.
     */
    public function consecutiveDaysAboveThreshold(
        User $user,
        string $metricType,
        float $threshold,
        int $requiredDays = 3
    ): bool {
        $recentMetrics = HealthMetric::where('user_id', $user->id)
            ->where('metric_type', $metricType)
            ->where('date', '>=', Carbon::now()->subDays($requiredDays))
            ->orderBy('date', 'desc')
            ->take($requiredDays)
            ->get();

        if ($recentMetrics->count() < $requiredDays) {
            return false;
        }

        return $recentMetrics->every(fn($metric) => $metric->value > $threshold);
    }
}

