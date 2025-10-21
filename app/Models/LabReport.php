<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'report_date',
        'facility',
        'parsed_json',
        'status',
    ];

    protected $casts = [
        'report_date' => 'date',
        'parsed_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(LabResult::class);
    }
}

