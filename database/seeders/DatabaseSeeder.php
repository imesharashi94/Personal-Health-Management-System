<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Observation;
use App\Models\Report;
use App\Models\Session;
use App\Models\Tag;
use App\Models\Code;
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
            'preferred_language' => 'en',
            'preferences' => ['theme' => 'light'],
        ]);

        // Create demo user
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@phms.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'preferred_language' => 'en',
            'preferences' => ['theme' => 'light'],
        ]);

        // Seed tags and codes
        $this->seedTagsAndCodes();

        // Create sessions
        $this->seedSessions($user);

        // Seed observations for the last 90 days
        $this->seedObservations($user, 90);

        // Create reports with observations
        $this->seedReports($user, 3);
    }

    private function seedTagsAndCodes(): void
    {
        // Create tags
        $tags = [
            ['name' => 'Important', 'color' => '#ff6b6b', 'description' => 'Important reports'],
            ['name' => 'Follow-up', 'color' => '#4ecdc4', 'description' => 'Reports requiring follow-up'],
            ['name' => 'Normal', 'color' => '#45b7d1', 'description' => 'Normal results'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }

        // Create medical codes
        $codes = [
            ['code_system' => 'LOINC', 'code_value' => '33747-0', 'display_name' => 'Hemoglobin', 'description' => 'Hemoglobin measurement'],
            ['code_system' => 'LOINC', 'code_value' => '2089-1', 'display_name' => 'LDL Cholesterol', 'description' => 'Low-density lipoprotein cholesterol'],
            ['code_system' => 'LOINC', 'code_value' => '33747-1', 'display_name' => 'Blood Glucose', 'description' => 'Blood glucose level'],
        ];

        foreach ($codes as $code) {
            Code::create($code);
        }
    }

    private function seedSessions(User $user): void
    {
        // Create some demo sessions
        for ($i = 0; $i < 5; $i++) {
            Session::create([
                'user_id' => $user->id,
                'started_at' => Carbon::now()->subDays($i * 7),
                'ended_at' => Carbon::now()->subDays($i * 7)->addHours(2),
                'session_type' => 'data_entry',
                'metadata' => ['source' => 'web_app'],
            ]);
        }
    }

    private function seedObservations(User $user, int $days): void
    {
        $startDate = Carbon::now()->subDays($days);
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Steps
            Observation::create([
                'user_id' => $user->id,
                'observation_type' => 'health_metric',
                'metric_name' => 'steps',
                'value' => rand(3000, 12000),
                'unit' => 'steps',
                'observation_date' => $date,
                'source' => 'csv',
            ]);

            // Resting HR
            Observation::create([
                'user_id' => $user->id,
                'observation_type' => 'health_metric',
                'metric_name' => 'hr',
                'value' => rand(58, 78),
                'unit' => 'bpm',
                'observation_date' => $date,
                'source' => 'csv',
            ]);

            // Sleep
            Observation::create([
                'user_id' => $user->id,
                'observation_type' => 'health_metric',
                'metric_name' => 'sleep',
                'value' => rand(50, 90) / 10, // 5.0 - 9.0 hours
                'unit' => 'hours',
                'observation_date' => $date,
                'source' => 'csv',
            ]);
        }
    }

    private function seedReports(User $user, int $count): void
    {
        $tags = Tag::all();
        
        for ($i = 0; $i < $count; $i++) {
            $report = Report::create([
                'user_id' => $user->id,
                'file_path' => 'lab_reports/demo_' . ($i + 1) . '.pdf',
                'report_date' => Carbon::now()->subMonths($i * 2),
                'facility' => 'Demo Medical Center',
                'status' => 'parsed',
                'report_type' => 'lab_report',
                'parsed_json' => [
                    'text' => 'Demo lab report content',
                ],
                'metadata' => ['source' => 'demo'],
            ]);

            // Attach random tags to reports
            if ($tags->count() > 0) {
                $report->tags()->attach($tags->random(rand(1, 2))->pluck('id'));
            }

            // LDL
            $ldlValue = rand(90, 170);
            Observation::create([
                'user_id' => $user->id,
                'report_id' => $report->id,
                'observation_type' => 'lab_result',
                'metric_name' => 'LDL',
                'value' => $ldlValue,
                'unit' => 'mg/dL',
                'ref_low' => 100,
                'ref_high' => 130,
                'flagged' => $ldlValue > 130,
                'observation_date' => $report->report_date,
                'source' => 'lab_report',
            ]);

            // Hemoglobin
            $hbValue = rand(120, 170) / 10;
            Observation::create([
                'user_id' => $user->id,
                'report_id' => $report->id,
                'observation_type' => 'lab_result',
                'metric_name' => 'Hemoglobin',
                'value' => $hbValue,
                'unit' => 'g/dL',
                'ref_low' => 12.0,
                'ref_high' => 16.0,
                'flagged' => $hbValue < 12.0 || $hbValue > 16.0,
                'observation_date' => $report->report_date,
                'source' => 'lab_report',
            ]);
        }
    }
}
