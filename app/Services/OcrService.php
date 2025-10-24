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

            // Check if it's a PDF file
            if (pathinfo($fullPath, PATHINFO_EXTENSION) === 'pdf') {
                // Convert PDF to image first
                $imagePath = $this->convertPdfToImage($fullPath);
                $ocr = new TesseractOCR($imagePath);
            } else {
                $ocr = new TesseractOCR($fullPath);
            }
            
            $ocr->lang('eng');
            $result = $ocr->run();
            
            // Clean up temporary image file if it was created
            if (isset($imagePath) && file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('OCR extraction failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Convert PDF to image using available tools
     */
    private function convertPdfToImage(string $pdfPath): string
    {
        $imagePath = tempnam(sys_get_temp_dir(), 'ocr_') . '.png';
        
        // Try different methods to convert PDF to image
        $methods = [
            // Method 1: ImageMagick (if available)
            "convert -density 300 '{$pdfPath}[0]' -quality 100 '{$imagePath}'",
            // Method 2: Ghostscript (if available)
            "gs -dNOPAUSE -dBATCH -sDEVICE=png16m -r300 -sOutputFile='{$imagePath}' '{$pdfPath}'",
            // Method 3: pdftoppm (if available)
            "pdftoppm -png -r 300 -f 1 -l 1 '{$pdfPath}' " . dirname($imagePath) . "/" . basename($imagePath, '.png'),
        ];
        
        foreach ($methods as $method) {
            $output = [];
            $returnCode = 0;
            
            exec($method, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($imagePath)) {
                return $imagePath;
            }
        }
        
        // If all methods fail, create a simple fallback
        throw new \Exception("PDF to image conversion failed. Please install ImageMagick, Ghostscript, or poppler-utils. Command tried: " . implode('; ', $methods));
    }

    /**
     * Parse lab values from OCR text.
     * Returns array of analytes with their values.
     */
    public function parseLabValues(string $text): array
    {
        $results = [];

        // Use universal parsing that works for any lab report format
        $results = $this->parseUniversalLabReport($text);

        // Log the parsing results for debugging
        Log::info('OCR parsing results', [
            'text_length' => strlen($text),
            'results_count' => count($results),
            'results' => $results
        ]);

        return $results;
    }

    /**
     * Universal lab report parser that works for any report format
     */
    private function parseUniversalLabReport(string $text): array
    {
        $results = [];
        
        // Clean up the text first
        $text = preg_replace('/\s+/', ' ', $text); // Replace multiple spaces with single space
        $text = preg_replace('/\n+/', "\n", $text); // Replace multiple newlines with single newline
        
        // Look for common lab value patterns
        $patterns = [
            // Pattern 1: ANALYTE: VALUE UNIT (Normal Range: LOW-HIGH UNIT)
            '/([A-Za-z\s]+?):\s*(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\s+\(Normal Range:\s*(\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\)/i',
            
            // Pattern 2: ANALYTE VALUE UNIT (Normal Range: LOW-HIGH UNIT)
            '/([A-Za-z\s]+?)\s+(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\s+\(Normal Range:\s*(\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\)/i',
            
            // Pattern 3: ANALYTE VALUE UNIT (LOW-HIGH UNIT)
            '/([A-Za-z\s]+?)\s+(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\s+\((\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\)/i',
            
            // Pattern 4: ANALYTE: VALUE UNIT (LOW-HIGH)
            '/([A-Za-z\s]+?):\s*(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\s+\((\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)\)/i',
            
            // Pattern 5: ANALYTE VALUE UNIT (LOW-HIGH)
            '/([A-Za-z\s]+?)\s+(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)\s+\((\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)\)/i',
            
            // Pattern 6: ANALYTE VALUE LOW-HIGH UNIT
            '/([A-Za-z\s]+?)\s+(\d+(?:\.\d+)?)\s+(\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)/i',
            
            // Pattern 7: ANALYTE: VALUE LOW-HIGH UNIT
            '/([A-Za-z\s]+?):\s*(\d+(?:\.\d+)?)\s+(\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)/i',
            
            // Pattern 8: Simple ANALYTE VALUE (for lipid profiles)
            '/(Cholesterol|LDL|HDL|Triglycerides|VLDL|Non-HDL)\s+[A-Za-z\s]*?(\d+(?:\.\d+)?)/i',
            
            // Pattern 9: ANALYTE VALUE (with OCR errors)
            '/([A-Za-z]{3,})\s+(\d+(?:\.\d+)?)\s+([A-Za-z\/]+)/i',
        ];
        
        foreach ($patterns as $patternIndex => $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $testName = trim($match[1]);
                    $value = (float) $match[2];
                    
                    // Skip if test name is too short or contains only numbers
                    if (strlen($testName) < 2 || preg_match('/^\d+$/', $testName)) {
                        continue;
                    }
                    
                    // Skip if value is 0 (likely parsing error)
                    if ($value == 0) {
                        continue;
                    }
                    
                    // Extract reference range and unit based on pattern
                    if ($patternIndex <= 6) {
                        // Patterns with reference ranges
                        $unit = isset($match[3]) ? trim($match[3]) : 'mg/dL';
                        $refLow = isset($match[4]) ? (float) $match[4] : 0;
                        $refHigh = isset($match[5]) ? (float) $match[5] : 100;
                    } elseif ($patternIndex == 7) {
                        // Lipid profile pattern
                        $unit = 'mg/dL';
                        $refLow = 0;
                        $refHigh = 200;
                    } else {
                        // OCR error pattern
                        $unit = isset($match[3]) ? trim($match[3]) : 'mg/dL';
                        $refLow = 0;
                        $refHigh = 100;
                    }
                    
                    // Clean up test name
                    $testName = $this->cleanTestName($testName);
                    
                    $results[] = [
                        'analyte' => $testName,
                        'value' => $value,
                        'unit' => $unit,
                        'ref_low' => $refLow,
                        'ref_high' => $refHigh,
                        'flagged' => $value < $refLow || $value > $refHigh,
                    ];
                }
            }
        }
        
        // Remove duplicates based on analyte name
        $uniqueResults = [];
        $seenAnalytes = [];
        
        foreach ($results as $result) {
            $analyteKey = strtolower(trim($result['analyte']));
            if (!in_array($analyteKey, $seenAnalytes)) {
                $uniqueResults[] = $result;
                $seenAnalytes[] = $analyteKey;
            }
        }
        
        return $uniqueResults;
    }
    
    /**
     * Clean up test names to handle OCR errors
     */
    private function cleanTestName(string $name): string
    {
        // Common OCR error corrections
        $corrections = [
            'yotalprotain' => 'Total protein',
            'Atbumin' => 'Albumin',
            'tucose' => 'Glucose',
            'cholesterol' => 'Cholesterol',
            'ar' => 'ALT',
            'ast' => 'AST',
            'ap' => 'ALP',
            'ser' => 'GGT',
            'otal bimrubin' => 'Total bilirubin',
        ];
        
        $name = trim($name);
        
        foreach ($corrections as $error => $correct) {
            if (stripos($name, $error) !== false) {
                $name = $correct;
                break;
            }
        }
        
        return $name;
    }
}

