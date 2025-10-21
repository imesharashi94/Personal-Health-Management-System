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
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_report_id')->constrained()->onDelete('cascade');
            $table->string('analyte', 120);
            $table->decimal('value', 10, 2)->nullable();
            $table->string('unit', 40)->nullable();
            $table->decimal('ref_low', 10, 2)->nullable();
            $table->decimal('ref_high', 10, 2)->nullable();
            $table->boolean('flagged')->default(false);
            $table->timestamps();

            $table->index(['lab_report_id', 'analyte']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};

