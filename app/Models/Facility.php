<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    protected $fillable = [
        'university_id',
        'type',
        'name',
        'description',
        'is_available',
        'data_status',
        'source_note',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
