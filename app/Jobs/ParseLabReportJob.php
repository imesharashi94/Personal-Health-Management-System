<?php

namespace App\Jobs;

use App\Models\LabReport;
use App\Models\LabResult;
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
        public int $labReportId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OcrService $ocrService): void
    {
        $labReport = LabReport::find($this->labReportId);

        if (!$labReport) {
            Log::error('Lab report not found', ['id' => $this->labReportId]);
            return;
        }

        try {
            // Extract text from the file
            $text = $ocrService->extractText($labReport->file_path);

            // Parse lab values from the text
            $parsedValues = $ocrService->parseLabValues($text);

            // Store the raw OCR text
            $labReport->update([
                'parsed_json' => ['text' => $text],
                'status' => 'parsed',
            ]);

            // Create lab results
            foreach ($parsedValues as $result) {
                LabResult::create([
                    'lab_report_id' => $labReport->id,
                    'analyte' => $result['analyte'],
                    'value' => $result['value'],
                    'unit' => $result['unit'],
                    'ref_low' => $result['ref_low'],
                    'ref_high' => $result['ref_high'],
                    'flagged' => $result['flagged'],
                ]);
            }

            Log::info('Lab report parsed successfully', [
                'lab_report_id' => $labReport->id,
                'results_count' => count($parsedValues),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse lab report', [
                'lab_report_id' => $labReport->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $labReport->update(['status' => 'failed']);
        }
    }
}

