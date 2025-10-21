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
        Schema::create('health_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('metric_type', ['steps', 'hr', 'sleep']);
            $table->decimal('value', 10, 2);
            $table->string('unit', 20)->nullable();
            $table->string('source', 40)->default('csv');
            $table->timestamps();

            $table->unique(['user_id', 'date', 'metric_type', 'source']);
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_metrics');
    }
};

