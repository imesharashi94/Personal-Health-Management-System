<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Log;

class OcrService
{
    /**
     * Extract text from an image or PDF using Tesseract OCR.
     */
    public function extractText(string $path): string
    {
        try {
            $fullPath = storage_path('app/' . $path);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("File not found: {$fullPath}");
            }

            $ocr = new TesseractOCR($fullPath);
            $ocr->lang('eng');
            
            return $ocr->run();
        } catch (\Exception $e) {
            Log::error('OCR extraction failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Parse lab values from OCR text.
     * Returns array of analytes with their values.
     */
    public function parseLabValues(string $text): array
    {
        $results = [];

        // Define patterns for common analytes
        $patterns = [
            'LDL' => [
                'pattern' => '/LDL[^0-9]*(\d+(?:\.\d+)?)/i',
                'unit' => 'mg/dL',
                'ref_low' => 100,
                'ref_high' => 130,
            ],
            'Hemoglobin' => [
                'pattern' => '/Hemoglobin[^0-9]*(\d+(?:\.\d+)?)/i',
                'unit' => 'g/dL',
                'ref_low' => 12.0,
                'ref_high' => 16.0,
            ],
            'Glucose' => [
                'pattern' => '/Glucose[^0-9]*(\d+(?:\.\d+)?)/i',
                'unit' => 'mg/dL',
                'ref_low' => 70,
                'ref_high' => 100,
            ],
            'Cholesterol' => [
                'pattern' => '/(?:Total\s+)?Cholesterol[^0-9]*(\d+(?:\.\d+)?)/i',
                'unit' => 'mg/dL',
                'ref_low' => 125,
                'ref_high' => 200,
            ],
            'HDL' => [
                'pattern' => '/HDL[^0-9]*(\d+(?:\.\d+)?)/i',
                'unit' => 'mg/dL',
                'ref_low' => 40,
                'ref_high' => 60,
            ],
            'Triglycerides' => [
                'pattern' => '/Triglycerides?[^0-9]*(\d+(?:\.\d+)?)/i',
                'unit' => 'mg/dL',
                'ref_low' => 0,
                'ref_high' => 150,
            ],
        ];

        foreach ($patterns as $analyte => $config) {
            if (preg_match($config['pattern'], $text, $matches)) {
                $value = (float) $matches[1];
                $flagged = $value < $config['ref_low'] || $value > $config['ref_high'];

                $results[] = [
                    'analyte' => $analyte,
                    'value' => $value,
                    'unit' => $config['unit'],
                    'ref_low' => $config['ref_low'],
                    'ref_high' => $config['ref_high'],
                    'flagged' => $flagged,
                ];
            }
        }

        return $results;
    }
}

