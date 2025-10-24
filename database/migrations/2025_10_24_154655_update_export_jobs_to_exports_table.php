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
        // This migration is handled by the table rename in the main migration
        // No additional changes needed for exports table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No additional changes needed for exports table
    }
};
