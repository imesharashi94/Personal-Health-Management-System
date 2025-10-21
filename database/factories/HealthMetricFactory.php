<?php

namespace Database\Factories;

use App\Models\HealthMetric;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthMetric>
 */
class HealthMetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $metricType = $this->faker->randomElement(['steps', 'hr', 'sleep']);
        
        $value = match($metricType) {
            'steps' => $this->faker->numberBetween(2000, 15000),
            'hr' => $this->faker->numberBetween(55, 85),
            'sleep' => $this->faker->randomFloat(2, 4, 9),
        };

        $unit = match($metricType) {
            'steps' => 'steps',
            'hr' => 'bpm',
            'sleep' => 'hours',
        };

        return [
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeBetween('-90 days', 'now')->format('Y-m-d'),
            'metric_type' => $metricType,
            'value' => $value,
            'unit' => $unit,
            'source' => $this->faker->randomElement(['csv', 'manual', 'api']),
        ];
    }

    public function steps(): static
    {
        return $this->state(fn (array $attributes) => [
            'metric_type' => 'steps',
            'value' => $this->faker->numberBetween(2000, 15000),
            'unit' => 'steps',
        ]);
    }

    public function heartRate(): static
    {
        return $this->state(fn (array $attributes) => [
            'metric_type' => 'hr',
            'value' => $this->faker->numberBetween(55, 85),
            'unit' => 'bpm',
        ]);
    }

    public function sleep(): static
    {
        return $this->state(fn (array $attributes) => [
            'metric_type' => 'sleep',
            'value' => $this->faker->randomFloat(2, 4, 9),
            'unit' => 'hours',
        ]);
    }
}

