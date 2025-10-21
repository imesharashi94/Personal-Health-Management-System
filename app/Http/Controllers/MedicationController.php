<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicationRequest;
use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $medications = Medication::where('user_id', $user->id)
            ->with('schedules')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Medications/Index', [
            'medications' => $medications,
        ]);
    }

    public function store(MedicationRequest $request)
    {
        $user = $request->user();

        $medication = Medication::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'dose' => $request->dose,
            'unit' => $request->unit,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Medication added successfully.');
    }

    public function update(MedicationRequest $request, Medication $medication)
    {
        Gate::authorize('update', $medication);

        $medication->update($request->validated());

        return back()->with('success', 'Medication updated successfully.');
    }

    public function destroy(Medication $medication)
    {
        Gate::authorize('delete', $medication);

        $medication->delete();

        return back()->with('success', 'Medication deleted successfully.');
    }
}

