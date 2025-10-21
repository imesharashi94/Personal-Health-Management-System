<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicationScheduleRequest;
use App\Models\Medication;
use App\Models\MedicationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MedicationScheduleController extends Controller
{
    public function store(MedicationScheduleRequest $request, Medication $medication)
    {
        Gate::authorize('update', $medication);

        $schedule = $medication->schedules()->create($request->validated());

        return back()->with('success', 'Schedule added successfully.');
    }

    public function update(MedicationScheduleRequest $request, MedicationSchedule $schedule)
    {
        Gate::authorize('update', $schedule->medication);

        $schedule->update($request->validated());

        return back()->with('success', 'Schedule updated successfully.');
    }

    public function destroy(MedicationSchedule $schedule)
    {
        Gate::authorize('delete', $schedule->medication);

        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully.');
    }
}

