<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeItem extends Model
{
    protected $fillable = [
        'program_id',
        'category',
        'label',
        'amount',
        'currency',
        'frequency',
        'academic_year',
        'data_status',
        'source_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
