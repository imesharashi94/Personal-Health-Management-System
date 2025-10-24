<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OcrBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'text_content',
        'coordinates',
        'confidence',
        'block_type',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'confidence' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function isHighConfidence(): bool
    {
        return $this->confidence >= 0.8;
    }

    public function isLowConfidence(): bool
    {
        return $this->confidence < 0.5;
    }
}
