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
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->string('code_system', 100);
            $table->string('code_value', 50);
            $table->string('display_name', 200);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['code_system', 'code_value']);
            $table->index('code_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
