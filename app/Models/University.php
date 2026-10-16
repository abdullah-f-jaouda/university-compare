<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    protected $fillable = [
        'name',
        'city',
        'address',
        'website',
        'phone',
        'description',
        'is_active',
        'data_status',
        'source_note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    /** يعرض فقط السجلات الفعّالة (غير الموقوفة) */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
