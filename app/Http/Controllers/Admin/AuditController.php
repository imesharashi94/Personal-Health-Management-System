<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index(Request $request)
    {
        $entity = $request->input('entity');
        $action = $request->input('action');

        $logs = AuditLog::query()
            ->with('user:id,name,email')
            ->when($entity, fn($query, $entity) => $query->where('entity', $entity))
            ->when($action, fn($query, $action) => $query->where('action', $action))
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return Inertia::render('Admin/Audit', [
            'logs' => $logs,
            'filters' => [
                'entity' => $entity,
                'action' => $action,
            ],
        ]);
    }
}

