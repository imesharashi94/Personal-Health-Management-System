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
        Schema::table('lab_reports', function (Blueprint $table) {
            $table->string('report_type', 50)->default('lab_report');
            $table->json('metadata')->nullable();
            $table->foreignId('session_id')->nullable()->constrained('sessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_reports', function (Blueprint $table) {
            $table->dropColumn(['report_type', 'metadata', 'session_id']);
        });
    }
};
