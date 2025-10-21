<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadReportRequest;
use App\Jobs\ParseLabReportJob;
use App\Models\AuditLog;
use App\Models\LabReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LabController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $reports = LabReport::where('user_id', $user->id)
            ->with('results')
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Labs/Index', [
            'reports' => $reports,
        ]);
    }

    public function show(Request $request, LabReport $labReport)
    {
        Gate::authorize('view', $labReport);

        $labReport->load('results');

        // Audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'view',
            'entity' => 'lab_report',
            'entity_id' => $labReport->id,
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return Inertia::render('Labs/Show', [
            'report' => $labReport,
        ]);
    }

    public function upload(UploadReportRequest $request)
    {
        $user = $request->user();

        // Store the uploaded file
        $file = $request->file('file');
        $path = $file->store('lab_reports', 'local');

        // Create lab report record
        $labReport = LabReport::create([
            'user_id' => $user->id,
            'file_path' => $path,
            'report_date' => $request->report_date,
            'facility' => $request->facility,
            'status' => 'uploaded',
        ]);

        // Dispatch parsing job
        ParseLabReportJob::dispatch($labReport->id);

        return back()->with('success', 'Lab report uploaded successfully. OCR parsing is in progress.');
    }

    public function parse(Request $request, LabReport $labReport)
    {
        Gate::authorize('update', $labReport);

        // Re-queue parsing job
        ParseLabReportJob::dispatch($labReport->id);

        return back()->with('success', 'Lab report parsing has been re-queued.');
    }
}

