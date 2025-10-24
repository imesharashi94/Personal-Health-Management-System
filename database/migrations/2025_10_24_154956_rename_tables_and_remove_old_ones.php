<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename existing tables to match new ER diagram
        Schema::rename('lab_reports', 'reports');
        Schema::rename('export_jobs', 'exports');
        
        // Drop old tables that are now consolidated into observations
        Schema::dropIfExists('health_metrics');
        Schema::dropIfExists('lab_results');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename tables back
        Schema::rename('reports', 'lab_reports');
        Schema::rename('exports', 'export_jobs');
        
        // Note: We don't recreate health_metrics and lab_results in down()
        // as they would need data migration which is complex to reverse
    }
};
