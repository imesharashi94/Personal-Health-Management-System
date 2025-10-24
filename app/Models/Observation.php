<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Observation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_id',
        'observation_type',
        'metric_name',
        'value',
        'unit',
        'ref_low',
        'ref_high',
        'flagged',
        'observation_date',
        'source',
    ];

    protected $casts = [
        'observation_date' => 'date',
        'value' => 'decimal:2',
        'ref_low' => 'decimal:2',
        'ref_high' => 'decimal:2',
        'flagged' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Check if observation is a health metric
     */
    public function isHealthMetric(): bool
    {
        return $this->observation_type === 'health_metric';
    }

    /**
     * Check if observation is a lab result
     */
    public function isLabResult(): bool
    {
        return $this->observation_type === 'lab_result';
    }

    /**
     * Check if observation is flagged
     */
    public function isFlagged(): bool
    {
        return $this->flagged;
    }

    /**
     * Check if observation is within normal range
     */
    public function isWithinNormalRange(): bool
    {
        if (!$this->ref_low || !$this->ref_high) {
            return true; // No reference range defined
        }

        return $this->value >= $this->ref_low && $this->value <= $this->ref_high;
    }
}
