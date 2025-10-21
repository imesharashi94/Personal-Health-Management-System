<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Evaluate alerts daily at 1:30 AM
        $schedule->job(new \App\Jobs\EvaluateAlertsJob())->dailyAt('01:30');

        // Clean up old export files (older than 7 days)
        $schedule->call(function () {
            $files = \Illuminate\Support\Facades\Storage::files('exports');
            foreach ($files as $file) {
                if (\Illuminate\Support\Facades\Storage::lastModified($file) < now()->subDays(7)->timestamp) {
                    \Illuminate\Support\Facades\Storage::delete($file);
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
