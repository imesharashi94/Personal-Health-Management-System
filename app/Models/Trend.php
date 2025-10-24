<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trend extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'metric_type',
        'trend_value',
        'trend_direction',
        'calculated_at',
    ];

    protected $casts = [
        'trend_value' => 'decimal:2',
        'calculated_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isIncreasing(): bool
    {
        return $this->trend_direction === 'increasing';
    }

    public function isDecreasing(): bool
    {
        return $this->trend_direction === 'decreasing';
    }

    public function isStable(): bool
    {
        return $this->trend_direction === 'stable';
    }
}
