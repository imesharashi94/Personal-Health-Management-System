<?php

namespace App\Http\Controllers;

use App\Http\Requests\SymptomRequest;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SymptomController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $symptoms = Symptom::where('user_id', $user->id)
            ->orderBy('recorded_at', 'desc')
            ->paginate(20);

        return Inertia::render('Symptoms/Index', [
            'symptoms' => $symptoms,
        ]);
    }

    public function store(SymptomRequest $request)
    {
        $user = $request->user();

        $symptom = Symptom::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'severity' => $request->severity,
            'notes' => $request->notes,
            'recorded_at' => $request->recorded_at,
        ]);

        return back()->with('success', 'Symptom logged successfully.');
    }

    public function destroy(Symptom $symptom)
    {
        Gate::authorize('delete', $symptom);

        $symptom->delete();

        return back()->with('success', 'Symptom deleted successfully.');
    }
}

