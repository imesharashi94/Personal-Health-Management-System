<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Observation;
use App\Models\Symptom;
use App\Models\User;
use App\Notifications\AlertNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AlertService
{
    public function __construct(
        protected TrendService $trendService
    ) {}

    /**
     * Evaluate all alert rules for a user.
     */
    public function evaluateAlerts(User $user): array
    {
        $alerts = [];

        // Rule 1: Check for high LDL
        $ldlAlert = $this->checkHighLdl($user);
        if ($ldlAlert) {
            $alerts[] = $ldlAlert;
        }

        // Rule 2: Check for elevated heart rate
        $hrAlert = $this->checkElevatedHeartRate($user);
        if ($hrAlert) {
            $alerts[] = $hrAlert;
        }

        // Rule 3: Check for low sleep with severe symptoms
        $sleepAlert = $this->checkLowSleepWithSymptoms($user);
        if ($sleepAlert) {
            $alerts[] = $sleepAlert;
        }

        // Persist alerts and send notifications
        foreach ($alerts as $alertData) {
            $this->createAlert($user, $alertData);
        }

        return $alerts;
    }

    /**
     * Check if latest LDL is above threshold.
     */
    private function checkHighLdl(User $user): ?array
    {
        $latestLdl = Observation::where('user_id', $user->id)
            ->where('observation_type', 'lab_result')
            ->where('metric_name', 'LDL')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestLdl && $latestLdl->value > 160) {
            return [
                'type' => 'labs',
                'level' => 'warn',
                'message' => "Your LDL cholesterol is high ({$latestLdl->value} mg/dL). Consider consulting your healthcare provider.",
            ];
        }

        return null;
    }

    /**
     * Check if heart rate is elevated for consecutive days.
     */
    private function checkElevatedHeartRate(User $user): ?array
    {
        $baseline = $this->trendService->calculateBaseline($user, 'hr', 30);
        
        if ($baseline == 0) {
            return null; // Not enough data
        }

        $threshold = $baseline + 20;
        $isElevated = $this->trendService->consecutiveDaysAboveThreshold($user, 'hr', $threshold, 3);

        if ($isElevated) {
            return [
                'type' => 'hr',
                'level' => 'warn',
                'message' => "Your resting heart rate has been elevated for 3 consecutive days (baseline: {$baseline} bpm). Consider monitoring your stress levels and rest.",
            ];
        }

        return null;
    }

    /**
     * Check for low sleep combined with severe symptoms.
     */
    private function checkLowSleepWithSymptoms(User $user): ?array
    {
        // Check recent sleep
        $recentSleep = Observation::where('user_id', $user->id)
            ->where('observation_type', 'health_metric')
            ->where('metric_name', 'sleep')
            ->where('observation_date', '>=', Carbon::now()->subDays(1))
            ->first();

        if (!$recentSleep || $recentSleep->value >= 5) {
            return null;
        }

        // Check for severe symptoms
        $severeSymptom = Symptom::where('user_id', $user->id)
            ->where('severity', '>=', 6)
            ->where('recorded_at', '>=', Carbon::now()->subDays(1))
            ->exists();

        if ($severeSymptom) {
            return [
                'type' => 'self_care',
                'level' => 'info',
                'message' => "You slept less than 5 hours and logged a severe symptom. Prioritize rest and self-care today.",
            ];
        }

        return null;
    }

    /**
     * Create and persist an alert.
     */
    private function createAlert(User $user, array $alertData): Alert
    {
        // Check if similar alert already exists and is not resolved
        $existing = Alert::where('user_id', $user->id)
            ->where('type', $alertData['type'])
            ->whereNull('resolved_at')
            ->where('triggered_at', '>=', Carbon::now()->subDays(7))
            ->first();

        if ($existing) {
            return $existing; // Don't create duplicate
        }

        $alert = Alert::create([
            'user_id' => $user->id,
            'type' => $alertData['type'],
            'message' => $alertData['message'],
            'level' => $alertData['level'],
            'triggered_at' => now(),
            'observation_id' => $alertData['observation_id'] ?? null,
        ]);

        // Send notification
        try {
            $user->notify(new AlertNotification($alert));
        } catch (\Exception $e) {
            Log::error('Failed to send alert notification', [
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $alert;
    }

    /**
     * Resolve an alert.
     */
    public function resolveAlert(Alert $alert): void
    {
        $alert->update(['resolved_at' => now()]);
    }
}

