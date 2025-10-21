<?php

namespace Database\Factories;

use App\Models\LabReport;
use App\Models\LabResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabResult>
 */
class LabResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $analyte = $this->faker->randomElement(['LDL', 'Hemoglobin', 'Glucose', 'Cholesterol']);
        
        [$value, $unit, $refLow, $refHigh] = match($analyte) {
            'LDL' => [$this->faker->numberBetween(80, 180), 'mg/dL', 100, 130],
            'Hemoglobin' => [$this->faker->randomFloat(1, 11, 18), 'g/dL', 12.0, 16.0],
            'Glucose' => [$this->faker->numberBetween(70, 140), 'mg/dL', 70, 100],
            'Cholesterol' => [$this->faker->numberBetween(150, 250), 'mg/dL', 125, 200],
        };

        $flagged = $value < $refLow || $value > $refHigh;

        return [
            'lab_report_id' => LabReport::factory(),
            'analyte' => $analyte,
            'value' => $value,
            'unit' => $unit,
            'ref_low' => $refLow,
            'ref_high' => $refHigh,
            'flagged' => $flagged,
        ];
    }
}

