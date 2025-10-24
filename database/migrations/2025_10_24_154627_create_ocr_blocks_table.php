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
        Schema::create('ocr_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('lab_reports')->onDelete('cascade');
            $table->text('text_content');
            $table->json('coordinates');
            $table->decimal('confidence', 5, 2);
            $table->string('block_type', 50);
            $table->timestamps();
            
            $table->index(['report_id', 'block_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocr_blocks');
    }
};
