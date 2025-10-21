<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?int $userId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AlertService $alertService): void
    {
        try {
            if ($this->userId) {
                // Evaluate for a specific user
                $user = User::find($this->userId);
                
                if ($user) {
                    $alerts = $alertService->evaluateAlerts($user);
                    
                    Log::info('Alerts evaluated for user', [
                        'user_id' => $user->id,
                        'alerts_count' => count($alerts),
                    ]);
                }
            } else {
                // Evaluate for all active users
                User::whereNotNull('email_verified_at')
                    ->chunk(100, function ($users) use ($alertService) {
                        foreach ($users as $user) {
                            try {
                                $alertService->evaluateAlerts($user);
                            } catch (\Exception $e) {
                                Log::error('Failed to evaluate alerts for user', [
                                    'user_id' => $user->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    });

                Log::info('Alerts evaluated for all users');
            }
        } catch (\Exception $e) {
            Log::error('Failed to evaluate alerts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}

