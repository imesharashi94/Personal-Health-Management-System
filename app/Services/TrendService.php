<?php

namespace App\Services;

use App\Models\Observation;
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

        $avg = Observation::where('user_id', $user->id)
            ->where('observation_type', 'health_metric')
            ->where('metric_name', $metricType)
            ->where('observation_date', '>=', $startDate)
            ->avg('value');

        return round((float) $avg, 2);
    }

    /**
     * Calculate baseline (median) for resting heart rate.
     */
    public function calculateBaseline(User $user, string $metricType = 'hr', int $days = 30): float
    {
        $startDate = Carbon::now()->subDays($days);

        $values = Observation::where('user_id', $user->id)
            ->where('observation_type', 'health_metric')
            ->where('metric_name', $metricType)
            ->where('observation_date', '>=', $startDate)
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

        $observations = Observation::where('user_id', $user->id)
            ->where('observation_type', 'health_metric')
            ->where('metric_name', $metricType)
            ->where('observation_date', '>=', $startDate)
            ->orderBy('observation_date', 'asc')
            ->get(['observation_date', 'value']);

        return $observations->map(function ($observation) {
            return [
                'date' => $observation->observation_date->format('M d'),
                'value' => (float) $observation->value,
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
        $recentObservations = Observation::where('user_id', $user->id)
            ->where('observation_type', 'health_metric')
            ->where('metric_name', $metricType)
            ->where('observation_date', '>=', Carbon::now()->subDays($requiredDays))
            ->orderBy('observation_date', 'desc')
            ->take($requiredDays)
            ->get();

        if ($recentObservations->count() < $requiredDays) {
            return false;
        }

        return $recentObservations->every(fn($observation) => $observation->value > $threshold);
    }
}

