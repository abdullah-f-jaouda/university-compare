<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonCriterion extends Model
{
    // Laravel يجمّع اسم الجدول تلقائيًا بشكل خاطئ لهذا الاسم، فنحدده صراحة
    protected $table = 'comparison_criteria';

    protected $fillable = [
        'key',
        'name',
        'description',
        'direction',
        'default_weight',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
