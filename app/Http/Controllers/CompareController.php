<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Services\CostCalculator;
use App\Services\CostComparison;
use App\Support\Labels;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class CompareController extends Controller
{
    public function show(Request $request, CostCalculator $calculator, CostComparison $comparison)
    {
        $validator = Validator::make($request->query(), [
            'programs' => ['required', 'array', 'min:2', 'max:3'],
            'programs.*' => ['integer', 'distinct', 'exists:programs,id'],
        ], [
            'programs.required' => 'اختر برنامجين على الأقل للمقارنة.',
            'programs.array' => 'اختر برنامجين على الأقل للمقارنة.',
            'programs.min' => 'اختر برنامجين على الأقل للمقارنة.',
            'programs.max' => 'يمكن مقارنة 3 برامج كحد أقصى.',
            'programs.*.integer' => 'اختيار البرامج غير صالح.',
            'programs.*.distinct' => 'لا يمكن اختيار البرنامج نفسه مرتين.',
            'programs.*.exists' => 'أحد البرامج المحددة غير موجود.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('programs.index')->withErrors($validator);
        }

        $ids = array_map('intval', $request->query('programs'));

        // نحافظ على ترتيب الاختيار: أول برنامج هو "الأساس" في تحليل الفرق
        $programs = Program::active()
            ->whereHas('university', fn ($q) => $q->where('is_active', true))
            ->with(['university.facilities', 'feeItems', 'courses'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($program) => array_search($program->id, $ids))
            ->values();

        if ($programs->count() !== count($ids)) {
            return redirect()->route('programs.index')
                ->withErrors(['programs' => 'بعض البرامج المحددة غير متاحة حاليًا.']);
        }

        $columns = $programs->map(function (Program $program) use ($calculator) {
            $cost = $calculator->calculate($program);
            $courses = $program->courses;

            return [
                'program' => $program,
                'cost' => $cost,
                'by_category' => collect($cost['breakdown'])->pluck('subtotal', 'category')->all(),
                'facilities' => $program->university->facilities->where('is_available', true)->values(),
                // صفر مقررات مسجّلة يعني "غير متوفر" وليس صفرًا حقيقيًا
                'courses_count' => $courses->isEmpty() ? null : $courses->count(),
                'practical_count' => $courses->isEmpty() ? null : $courses->where('course_type', 'practical')->count(),
            ];
        });

        $rows = [
            $this->row('الجامعة', $columns, fn ($c) => $c['program']->university->name),
            $this->row('الدرجة', $columns, fn ($c) => Labels::degree($c['program']->degree_type)),
            $this->row('المدة', $columns, fn ($c) => $c['program']->duration_years !== null ? (float) $c['program']->duration_years : null, ' سنة'),
            $this->row('الساعات المعتمدة', $columns, fn ($c) => $c['program']->total_credit_hours),
            $this->row('نسبة الجانب العملي', $columns, fn ($c) => $c['program']->practical_percentage, '%'),
            $this->row('الحد الأدنى للقبول', $columns, fn ($c) => $c['program']->min_admission_percentage !== null ? (float) $c['program']->min_admission_percentage : null, '%'),
            $this->row('المقررات المسجّلة', $columns, fn ($c) => $c['courses_count']),
            $this->row('منها مقررات عملية', $columns, fn ($c) => $c['practical_count']),
        ];

        // الفئات التي تظهر في جدول التفصيل: المتوفرة في برنامج واحد على الأقل
        $categories = collect(CostCalculator::EXPECTED_CATEGORIES)
            ->filter(fn ($category) => $columns->contains(fn ($c) => isset($c['by_category'][$category])))
            ->values()
            ->all();

        $base = $columns->first();

        $analyses = $columns->slice(1)->map(fn ($other) => [
            'program' => $other['program'],
            'result' => $comparison->analyze($base['cost'], $other['cost']),
        ])->values();

        return view('compare.show', compact('columns', 'rows', 'categories', 'base', 'analyses'));
    }

    /**
     * صف مقارنة: القيم الفارغة (null) تعني "غير متوفر" ولا تُعامل كصفر،
     * و differs تعني اختلاف القيم المتوفرة (دون الحكم أيها أفضل).
     */
    private function row(string $label, Collection $columns, Closure $value, string $suffix = ''): array
    {
        $raw = $columns->map(fn ($column) => $value($column))->values()->all();
        $present = array_filter($raw, fn ($v) => $v !== null);

        return [
            'label' => $label,
            'display' => array_map(fn ($v) => $v === null ? null : $v . $suffix, $raw),
            'differs' => count(array_unique($present)) > 1,
            'has_missing' => count($present) < count($raw),
        ];
    }
}
