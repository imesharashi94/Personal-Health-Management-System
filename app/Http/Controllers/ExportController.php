<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportRequest;
use App\Models\ExportJob;
use App\Models\HealthMetric;
use App\Models\LabReport;
use App\Models\Medication;
use App\Models\Symptom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $exports = ExportJob::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Export/Index', [
            'exports' => $exports,
        ]);
    }

    public function run(ExportRequest $request)
    {
        $user = $request->user();
        
        $params = $request->validated();
        
        // Create export job record
        $exportJob = ExportJob::create([
            'user_id' => $user->id,
            'params' => $params,
            'status' => 'running',
        ]);

        try {
            // Generate CSV
            $csv = $this->generateCsv($user->id, $params);
            
            // Store file
            $filename = 'export_' . $exportJob->id . '_' . now()->format('Y-m-d_His') . '.csv';
            $path = 'exports/' . $filename;
            Storage::put($path, $csv);

            // Update export job
            $exportJob->update([
                'file_path' => $path,
                'status' => 'done',
                'completed_at' => now(),
            ]);

            return back()->with('success', 'Export completed successfully.');
        } catch (\Exception $e) {
            $exportJob->update(['status' => 'failed']);
            
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function download(ExportJob $exportJob)
    {
        if ($exportJob->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$exportJob->file_path || !Storage::exists($exportJob->file_path)) {
            return back()->with('error', 'Export file not found.');
        }

        return Storage::download($exportJob->file_path);
    }

    private function generateCsv(int $userId, array $params): string
    {
        $startDate = Carbon::parse($params['start_date']);
        $endDate = Carbon::parse($params['end_date']);
        $metricTypes = $params['metric_types'];

        $rows = [];
        $rows[] = ['Type', 'Date', 'Name', 'Value', 'Unit', 'Notes'];

        // Health metrics
        if (in_array('steps', $metricTypes) || in_array('hr', $metricTypes) || in_array('sleep', $metricTypes)) {
            $metrics = HealthMetric::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('metric_type', array_intersect($metricTypes, ['steps', 'hr', 'sleep']))
                ->orderBy('date')
                ->get();

            foreach ($metrics as $metric) {
                $rows[] = [
                    'health_metric',
                    $metric->date->format('Y-m-d'),
                    $metric->metric_type,
                    $metric->value,
                    $metric->unit,
                    '',
                ];
            }
        }

        // Labs
        if (in_array('labs', $metricTypes)) {
            $labReports = LabReport::where('user_id', $userId)
                ->whereBetween('report_date', [$startDate, $endDate])
                ->with('results')
                ->get();

            foreach ($labReports as $report) {
                foreach ($report->results as $result) {
                    $rows[] = [
                        'lab_result',
                        $report->report_date->format('Y-m-d'),
                        $result->analyte,
                        $result->value,
                        $result->unit,
                        $result->flagged ? 'FLAGGED' : '',
                    ];
                }
            }
        }

        // Symptoms
        if (in_array('symptoms', $metricTypes)) {
            $symptoms = Symptom::where('user_id', $userId)
                ->whereBetween('recorded_at', [$startDate, $endDate])
                ->get();

            foreach ($symptoms as $symptom) {
                $rows[] = [
                    'symptom',
                    $symptom->recorded_at->format('Y-m-d H:i'),
                    $symptom->name,
                    $symptom->severity,
                    'severity (1-10)',
                    $symptom->notes,
                ];
            }
        }

        // Medications
        if (in_array('medications', $metricTypes)) {
            $medications = Medication::where('user_id', $userId)
                ->with('schedules')
                ->get();

            foreach ($medications as $medication) {
                foreach ($medication->schedules as $schedule) {
                    $rows[] = [
                        'medication',
                        '',
                        $medication->name,
                        $medication->dose,
                        $medication->unit,
                        "{$schedule->frequency} at {$schedule->time_of_day}",
                    ];
                }
            }
        }

        // Convert to CSV
        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}

