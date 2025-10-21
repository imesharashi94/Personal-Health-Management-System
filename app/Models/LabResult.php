<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_report_id',
        'analyte',
        'value',
        'unit',
        'ref_low',
        'ref_high',
        'flagged',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'ref_low' => 'decimal:2',
        'ref_high' => 'decimal:2',
        'flagged' => 'boolean',
    ];

    public function labReport(): BelongsTo
    {
        return $this->belongsTo(LabReport::class);
    }
}

