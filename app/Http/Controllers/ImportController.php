<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCsvRequest;
use App\Jobs\ImportMetricsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ImportController extends Controller
{
    public function index()
    {
        return Inertia::render('Import/Index');
    }

    public function upload(ImportCsvRequest $request)
    {
        $user = $request->user();
        
        // Store the uploaded file
        $file = $request->file('file');
        $path = $file->store('imports', 'local');

        // Dispatch the import job
        ImportMetricsJob::dispatch($user->id, $path);

        return back()->with('success', 'Import queued successfully. Your data will be processed shortly.');
    }
}

