<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public string $filePath
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ImportService $importService): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::error('User not found for import', ['user_id' => $this->userId]);
            return;
        }

        try {
            $fullPath = storage_path('app/' . $this->filePath);

            if (!file_exists($fullPath)) {
                throw new \Exception("File not found: {$fullPath}");
            }

            // Read the file
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
            
            if ($extension === 'csv') {
                $rows = $this->parseCsv($fullPath);
            } elseif ($extension === 'json') {
                $rows = $this->parseJson($fullPath);
            } else {
                throw new \Exception("Unsupported file format: {$extension}");
            }

            // Validate structure
            $errors = $importService->validateCsvStructure($rows);
            if (!empty($errors)) {
                throw new \Exception('Invalid file structure: ' . implode(', ', $errors));
            }

            // Map and upsert metrics
            $mapped = $importService->mapRows($rows);
            $count = $importService->upsertMetrics($user, $mapped, 'csv');

            Log::info('Metrics imported successfully', [
                'user_id' => $user->id,
                'count' => $count,
                'file' => $this->filePath,
            ]);

            // Optionally delete the file after import
            // Storage::delete($this->filePath);
        } catch (\Exception $e) {
            Log::error('Failed to import metrics', [
                'user_id' => $user->id,
                'file' => $this->filePath,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Parse CSV file.
     */
    private function parseCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');
        
        if ($handle === false) {
            throw new \Exception('Failed to open CSV file');
        }

        // Read header
        $headers = fgetcsv($handle);
        
        if ($headers === false) {
            fclose($handle);
            throw new \Exception('CSV file is empty');
        }

        // Read data rows
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($headers)) {
                $rows[] = array_combine($headers, $data);
            } else {
                $rows[] = $data; // Fallback to indexed array
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse JSON file.
     */
    private function parseJson(string $path): array
    {
        $content = file_get_contents($path);
        
        if ($content === false) {
            throw new \Exception('Failed to read JSON file');
        }

        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON: ' . json_last_error_msg());
        }

        return is_array($data) ? $data : [];
    }
}

