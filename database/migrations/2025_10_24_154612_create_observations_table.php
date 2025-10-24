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
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_id')->nullable()->constrained('lab_reports')->onDelete('cascade');
            $table->string('observation_type', 50); // 'health_metric' or 'lab_result'
            $table->string('metric_name', 100);
            $table->decimal('value', 10, 2);
            $table->string('unit', 20)->nullable();
            $table->decimal('ref_low', 10, 2)->nullable();
            $table->decimal('ref_high', 10, 2)->nullable();
            $table->boolean('flagged')->default(false);
            $table->date('observation_date');
            $table->string('source', 50)->default('manual');
            $table->timestamps();
            
            $table->index(['user_id', 'observation_date']);
            $table->index(['report_id', 'observation_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
