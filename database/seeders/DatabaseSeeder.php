<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\HealthMetric;
use App\Models\LabReport;
use App\Models\LabResult;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@phms.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create demo user
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@phms.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Seed health metrics for the last 90 days
        $this->seedHealthMetrics($user, 90);

        // Create lab reports with results
        $this->seedLabReports($user, 3);
    }

    private function seedHealthMetrics(User $user, int $days): void
    {
        $startDate = Carbon::now()->subDays($days);
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Steps
            HealthMetric::create([
                'user_id' => $user->id,
                'date' => $date,
                'metric_type' => 'steps',
                'value' => rand(3000, 12000),
                'unit' => 'steps',
                'source' => 'csv',
            ]);

            // Resting HR
            HealthMetric::create([
                'user_id' => $user->id,
                'date' => $date,
                'metric_type' => 'hr',
                'value' => rand(58, 78),
                'unit' => 'bpm',
                'source' => 'csv',
            ]);

            // Sleep
            HealthMetric::create([
                'user_id' => $user->id,
                'date' => $date,
                'metric_type' => 'sleep',
                'value' => rand(50, 90) / 10, // 5.0 - 9.0 hours
                'unit' => 'hours',
                'source' => 'csv',
            ]);
        }
    }

    private function seedLabReports(User $user, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $report = LabReport::create([
                'user_id' => $user->id,
                'file_path' => 'lab_reports/demo_' . ($i + 1) . '.pdf',
                'report_date' => Carbon::now()->subMonths($i * 2),
                'facility' => 'Demo Medical Center',
                'status' => 'parsed',
                'parsed_json' => [
                    'text' => 'Demo lab report content',
                ],
            ]);

            // LDL
            $ldlValue = rand(90, 170);
            LabResult::create([
                'lab_report_id' => $report->id,
                'analyte' => 'LDL',
                'value' => $ldlValue,
                'unit' => 'mg/dL',
                'ref_low' => 100,
                'ref_high' => 130,
                'flagged' => $ldlValue > 130,
            ]);

            // Hemoglobin
            $hbValue = rand(120, 170) / 10;
            LabResult::create([
                'lab_report_id' => $report->id,
                'analyte' => 'Hemoglobin',
                'value' => $hbValue,
                'unit' => 'g/dL',
                'ref_low' => 12.0,
                'ref_high' => 16.0,
                'flagged' => $hbValue < 12.0 || $hbValue > 16.0,
            ]);
        }
    }
}
