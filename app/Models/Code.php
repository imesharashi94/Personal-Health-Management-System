<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_system',
        'code_value',
        'display_name',
        'description',
    ];

    public function getFullCode(): string
    {
        return $this->code_system . ':' . $this->code_value;
    }
}
