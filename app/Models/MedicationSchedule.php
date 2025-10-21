<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'frequency',
        'time_of_day',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}

