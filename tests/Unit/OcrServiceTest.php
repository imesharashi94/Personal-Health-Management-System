<?php

namespace Tests\Unit;

use App\Services\OcrService;
use Tests\TestCase;

class OcrServiceTest extends TestCase
{
    protected OcrService $ocrService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ocrService = new OcrService();
    }

    /**
     * Test parsing lab values from OCR text
     */
    public function test_parse_lab_values_extracts_ldl(): void
    {
        $text = "LDL Cholesterol: 150 mg/dL";
        
        $results = $this->ocrService->parseLabValues($text);
        
        $this->assertCount(1, $results);
        $this->assertEquals('LDL', $results[0]['analyte']);
        $this->assertEquals(150, $results[0]['value']);
        $this->assertEquals('mg/dL', $results[0]['unit']);
    }

    public function test_parse_lab_values_extracts_hemoglobin(): void
    {
        $text = "Hemoglobin: 14.5 g/dL";
        
        $results = $this->ocrService->parseLabValues($text);
        
        $this->assertCount(1, $results);
        $this->assertEquals('Hemoglobin', $results[0]['analyte']);
        $this->assertEquals(14.5, $results[0]['value']);
    }

    public function test_parse_lab_values_flags_high_ldl(): void
    {
        $text = "LDL: 170 mg/dL"; // Above ref_high of 130
        
        $results = $this->ocrService->parseLabValues($text);
        
        $this->assertTrue($results[0]['flagged']);
    }

    public function test_parse_lab_values_handles_multiple_analytes(): void
    {
        $text = "LDL: 120 mg/dL\nHemoglobin: 13.0 g/dL\nGlucose: 95 mg/dL";
        
        $results = $this->ocrService->parseLabValues($text);
        
        $this->assertCount(3, $results);
    }

    public function test_parse_lab_values_returns_empty_for_no_matches(): void
    {
        $text = "This text has no lab values";
        
        $results = $this->ocrService->parseLabValues($text);
        
        $this->assertCount(0, $results);
    }
}

