<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadReportRequest;
use App\Jobs\ParseLabReportJob;
use App\Models\AuditLog;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LabController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $reports = Report::where('user_id', $user->id)
            ->with('observations')
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Labs/Index', [
            'reports' => $reports,
        ]);
    }

    public function show(Request $request, Report $report)
    {
        Gate::authorize('view', $report);

        $report->load('observations');

        // Audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'view',
            'entity' => 'report',
            'entity_id' => $report->id,
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return Inertia::render('Labs/Show', [
            'report' => $report,
        ]);
    }

    public function upload(UploadReportRequest $request)
    {
        logger($request->all());

        $user = $request->user();

        // Store the uploaded file
        $file = $request->file('file');
        $path = $file->store('lab_reports', 'local');

        // Create report record
        $report = Report::create([
            'user_id' => $user->id,
            'file_path' => $path,
            'report_date' => $request->report_date,
            'facility' => $request->facility,
            'status' => 'uploaded',
            'report_type' => 'lab_report',
        ]);

        // Dispatch parsing job
        ParseLabReportJob::dispatch($report->id);

        return back()->with('success', 'Lab report uploaded successfully. OCR parsing is in progress.');
    }

    public function parse(Request $request, Report $report)
    {
        Gate::authorize('update', $report);

        // Re-queue parsing job
        ParseLabReportJob::dispatch($report->id);

        return back()->with('success', 'Lab report parsing has been re-queued.');
    }
}

