<?php

namespace App\Services;

use App\Models\HealthMetric;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportService
{
    /**
     * Map CSV/JSON rows to health metrics format.
     * Expected format: date, metric_type, value, unit (optional)
     */
    public function mapRows(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $index => $row) {
            try {
                // Support both array and object formats
                $row = (array) $row;

                // Try to detect the format
                $date = $this->parseDate($row['date'] ?? $row[0] ?? null);
                $metricType = strtolower($row['metric'] ?? $row['metric_type'] ?? $row[1] ?? '');
                $value = $row['value'] ?? $row[2] ?? null;
                $unit = $row['unit'] ?? $row[3] ?? null;

                // Validate metric type
                if (!in_array($metricType, ['steps', 'hr', 'sleep'])) {
                    Log::warning("Invalid metric type at row {$index}: {$metricType}");
                    continue;
                }

                // Set default unit if not provided
                if (!$unit) {
                    $unit = match($metricType) {
                        'steps' => 'steps',
                        'hr' => 'bpm',
                        'sleep' => 'hours',
                        default => null,
                    };
                }

                $mapped[] = [
                    'date' => $date,
                    'metric_type' => $metricType,
                    'value' => (float) $value,
                    'unit' => $unit,
                ];
            } catch (\Exception $e) {
                Log::warning("Failed to map row {$index}", [
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $mapped;
    }

    /**
     * Upsert health metrics for a user.
     */
    public function upsertMetrics(User $user, array $metrics, string $source = 'csv'): int
    {
        $count = 0;

        foreach ($metrics as $metric) {
            try {
                HealthMetric::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'date' => $metric['date'],
                        'metric_type' => $metric['metric_type'],
                        'source' => $source,
                    ],
                    [
                        'value' => $metric['value'],
                        'unit' => $metric['unit'] ?? null,
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to upsert metric', [
                    'user_id' => $user->id,
                    'metric' => $metric,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($dateString): string
    {
        if (!$dateString) {
            throw new \Exception('Date is required');
        }

        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Invalid date format: {$dateString}");
        }
    }

    /**
     * Validate CSV structure.
     */
    public function validateCsvStructure(array $rows): array
    {
        $errors = [];

        if (empty($rows)) {
            $errors[] = 'CSV file is empty';
            return $errors;
        }

        // Check first row for headers
        $firstRow = (array) $rows[0];
        $hasHeaders = isset($firstRow['date']) || isset($firstRow['metric']);

        if (!$hasHeaders && count($firstRow) < 3) {
            $errors[] = 'CSV must have at least 3 columns: date, metric, value';
        }

        return $errors;
    }
}

