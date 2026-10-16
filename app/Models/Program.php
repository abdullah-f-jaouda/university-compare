<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'university_id',
        'name',
        'discipline',
        'degree_type',
        'duration_years',
        'total_credit_hours',
        'practical_percentage',
        'min_admission_percentage',
        'admission_notes',
        'description',
        'is_active',
        'data_status',
        'source_note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_years' => 'decimal:1',
        'min_admission_percentage' => 'decimal:2',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function feeItems(): HasMany
    {
        return $this->hasMany(FeeItem::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
