<?php

namespace App\Providers;

use App\Models\Report;
use App\Models\Medication;
use App\Models\Symptom;
use App\Policies\LabReportPolicy;
use App\Policies\MedicationPolicy;
use App\Policies\SymptomPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Report::class => LabReportPolicy::class,
        Medication::class => MedicationPolicy::class,
        Symptom::class => SymptomPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define admin gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
    }
}
