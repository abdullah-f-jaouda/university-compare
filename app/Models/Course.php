<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'program_id',
        'name',
        'code',
        'credit_hours',
        'semester',
        'course_type',
        'data_status',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
