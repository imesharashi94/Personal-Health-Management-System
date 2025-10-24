<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\Observation;
use App\Services\OcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseLabReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OcrService $ocrService): void
    {
        $report = Report::find($this->reportId);

        if (!$report) {
            Log::error('Report not found', ['id' => $this->reportId]);
            return;
        }

        try {
            // Extract text from the file using OCR
            $text = $ocrService->extractText($report->file_path);

            // Parse lab values from the text
            $parsedValues = $ocrService->parseLabValues($text);

            // Store the raw OCR text
            $report->update([
                'parsed_json' => [
                    'text' => $text,
                    'extracted_values' => $parsedValues,
                ],
                'status' => 'parsed',
            ]);

            // Create observations from parsed values
            foreach ($parsedValues as $result) {
                Observation::create([
                    'user_id' => $report->user_id,
                    'report_id' => $report->id,
                    'observation_type' => 'lab_result',
                    'metric_name' => $result['analyte'],
                    'value' => $result['value'],
                    'unit' => $result['unit'],
                    'ref_low' => $result['ref_low'],
                    'ref_high' => $result['ref_high'],
                    'flagged' => $result['flagged'],
                    'observation_date' => $report->report_date,
                    'source' => 'lab_report',
                ]);
            }

            Log::info('Report parsed successfully', [
                'report_id' => $report->id,
                'results_count' => count($parsedValues),
                'extracted_text_length' => strlen($text),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $report->update(['status' => 'failed']);
        }
    }
}

