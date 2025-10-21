<?php

namespace Tests\Unit;

use App\Services\ImportService;
use Tests\TestCase;

class ImportServiceTest extends TestCase
{
    protected ImportService $importService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService();
    }

    /**
     * Test mapping CSV rows to health metrics
     */
    public function test_map_rows_converts_valid_data(): void
    {
        $rows = [
            ['date' => '2025-10-21', 'metric' => 'steps', 'value' => '8500', 'unit' => 'steps'],
            ['date' => '2025-10-21', 'metric' => 'hr', 'value' => '72', 'unit' => 'bpm'],
        ];

        $mapped = $this->importService->mapRows($rows);

        $this->assertCount(2, $mapped);
        $this->assertEquals('steps', $mapped[0]['metric_type']);
        $this->assertEquals(8500, $mapped[0]['value']);
    }

    public function test_map_rows_skips_invalid_metric_types(): void
    {
        $rows = [
            ['date' => '2025-10-21', 'metric' => 'invalid_type', 'value' => '100', 'unit' => 'units'],
            ['date' => '2025-10-21', 'metric' => 'steps', 'value' => '8500', 'unit' => 'steps'],
        ];

        $mapped = $this->importService->mapRows($rows);

        $this->assertCount(1, $mapped); // Only valid metric
        $this->assertEquals('steps', $mapped[0]['metric_type']);
    }

    public function test_map_rows_adds_default_units(): void
    {
        $rows = [
            ['date' => '2025-10-21', 'metric' => 'steps', 'value' => '8500'],
        ];

        $mapped = $this->importService->mapRows($rows);

        $this->assertEquals('steps', $mapped[0]['unit']); // Default unit added
    }

    public function test_validate_csv_structure_detects_empty_file(): void
    {
        $rows = [];

        $errors = $this->importService->validateCsvStructure($rows);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('empty', $errors[0]);
    }

    public function test_validate_csv_structure_accepts_valid_data(): void
    {
        $rows = [
            ['date' => '2025-10-21', 'metric' => 'steps', 'value' => '8500'],
        ];

        $errors = $this->importService->validateCsvStructure($rows);

        $this->assertEmpty($errors);
    }
}

