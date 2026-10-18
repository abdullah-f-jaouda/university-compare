<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\University;
use App\Services\CostCalculator;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request, CostCalculator $calculator)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'university_id' => ['nullable', 'integer', 'exists:universities,id'],
            'discipline' => ['nullable', 'string', 'max:100'],
            'degree_type' => ['nullable', 'in:diploma,bachelor,master'],
            'max_duration' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'max_cost' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:name,cost,duration'],
        ]);

        $query = Program::active()
            ->whereHas('university', fn ($q) => $q->where('is_active', true))
            ->with(['university', 'feeItems']);

        if (! empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('discipline', 'like', $term)
                    ->orWhereHas('university', fn ($u) => $u->where('name', 'like', $term));
            });
        }

        if (! empty($filters['university_id'])) {
            $query->where('university_id', $filters['university_id']);
        }

        if (! empty($filters['discipline'])) {
            $query->where('discipline', $filters['discipline']);
        }

        if (! empty($filters['degree_type'])) {
            $query->where('degree_type', $filters['degree_type']);
        }

        if (isset($filters['max_duration']) && $filters['max_duration'] !== null) {
            $query->where('duration_years', '<=', $filters['max_duration']);
        }

        $programs = $query->get()
            ->each(fn ($program) => $program->cost = $calculator->calculate($program));

        // فلتر التكلفة يُطبَّق على الإجمالي المكتمل فقط؛ البرامج ذات الرسوم الناقصة أو غير المحسوبة
        // تُستبعد عند تحديد حد، لأن إجماليها الجزئي سيبدو أرخص مما هو فعليًا
        if (isset($filters['max_cost']) && $filters['max_cost'] !== null) {
            $programs = $programs->filter(
                fn ($program) => $program->cost['is_complete'] && $program->cost['total'] <= $filters['max_cost']
            );
        }

        $programs = match ($filters['sort'] ?? 'name') {
            // الإجمالي المكتمل أولًا (الأقل فالأعلى)، ثم الناقص أو غير المحسوب في النهاية
            'cost' => $programs->sortBy(fn ($p) => $p->cost['is_complete']
                ? $p->cost['total']
                : PHP_INT_MAX),
            'duration' => $programs->sortBy(fn ($p) => $p->duration_years ?? PHP_INT_MAX),
            default => $programs->sortBy('name'),
        };

        $programs = $programs->values();

        $universities = University::active()->orderBy('name')->get(['id', 'name']);
        $disciplines = Program::active()->distinct()->orderBy('discipline')->pluck('discipline');

        return view('programs.index', compact('programs', 'universities', 'disciplines'));
    }

    public function show(Program $program, CostCalculator $calculator)
    {
        $program->load(['university.facilities', 'feeItems', 'courses']);

        abort_unless($program->is_active && $program->university->is_active, 404);

        $cost = $calculator->calculate($program);
        $courses = $program->courses->sortBy('semester')->values();

        return view('programs.show', compact('program', 'cost', 'courses'));
    }
}
