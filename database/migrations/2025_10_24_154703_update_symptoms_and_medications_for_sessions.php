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
        Schema::table('symptoms', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->constrained('sessions')->onDelete('set null');
        });
        
        Schema::table('medications', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->constrained('sessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('symptoms', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
        
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
};
