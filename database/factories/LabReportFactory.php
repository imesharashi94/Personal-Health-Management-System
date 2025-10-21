<?php

namespace Database\Factories;

use App\Models\LabReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabReport>
 */
class LabReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'file_path' => 'lab_reports/' . $this->faker->uuid() . '.pdf',
            'report_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'facility' => $this->faker->company() . ' Medical Center',
            'parsed_json' => null,
            'status' => 'uploaded',
        ];
    }

    public function parsed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'parsed',
            'parsed_json' => [
                'ldl' => rand(80, 180),
                'hemoglobin' => rand(12, 17),
            ],
        ]);
    }
}

