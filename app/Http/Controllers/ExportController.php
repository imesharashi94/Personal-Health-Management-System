<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportRequest;
use App\Models\Export;
use App\Models\Observation;
use App\Models\Report;
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

        $exports = Export::where('user_id', $user->id)
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
        
        // Create export record
        $export = Export::create([
            'user_id' => $user->id,
            'params' => $params,
            'status' => 'running',
        ]);

        try {
            // Generate CSV
            $csv = $this->generateCsv($user->id, $params);
            
            // Store file
            $filename = 'export_' . $export->id . '_' . now()->format('Y-m-d_His') . '.csv';
            $path = 'exports/' . $filename;
            Storage::put($path, $csv);

            // Update export
            $export->update([
                'file_path' => $path,
                'status' => 'done',
                'completed_at' => now(),
            ]);

            return back()->with('success', 'Export completed successfully.');
        } catch (\Exception $e) {
            $export->update(['status' => 'failed']);
            
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function download(Export $export)
    {
        if ($export->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$export->file_path || !Storage::exists($export->file_path)) {
            return back()->with('error', 'Export file not found.');
        }

        return Storage::download($export->file_path);
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
            $observations = Observation::where('user_id', $userId)
                ->where('observation_type', 'health_metric')
                ->whereBetween('observation_date', [$startDate, $endDate])
                ->whereIn('metric_name', array_intersect($metricTypes, ['steps', 'hr', 'sleep']))
                ->orderBy('observation_date')
                ->get();

            foreach ($observations as $observation) {
                $rows[] = [
                    'health_metric',
                    $observation->observation_date->format('Y-m-d'),
                    $observation->metric_name,
                    $observation->value,
                    $observation->unit,
                    '',
                ];
            }
        }

        // Labs
        if (in_array('labs', $metricTypes)) {
            $observations = Observation::where('user_id', $userId)
                ->where('observation_type', 'lab_result')
                ->whereBetween('observation_date', [$startDate, $endDate])
                ->get();

            foreach ($observations as $observation) {
                $rows[] = [
                    'lab_result',
                    $observation->observation_date->format('Y-m-d'),
                    $observation->metric_name,
                    $observation->value,
                    $observation->unit,
                    $observation->flagged ? 'FLAGGED' : '',
                ];
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

