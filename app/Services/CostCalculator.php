<?php

namespace App\Services;

use App\Models\Program;

/**
 * حاسبة التكلفة التقديرية لبرنامج دراسي.
 *
 * الإجمالي = رسوم دراسية + رسوم تسجيل + رسوم معامل + رسوم أخرى إلزامية
 *
 * الافتراضات (تُعرض للمستخدم بوضوح):
 *  - السنة الدراسية = فصلان.
 *  - الرسم لكل فصل يُضرب في عدد الفصول، والرسم لكل سنة يُضرب في عدد السنوات
 *    (تُحتسب السنة التي بدأت كاملة)، والرسم لمرة واحدة يُحسب مرة وحدة.
 *  - البند غير المتوفر لا يُعامل كصفر: يُعلَّم الإجمالي "غير مكتمل".
 */
class CostCalculator
{
    public const SEMESTERS_PER_YEAR = 2;

    public const EXPECTED_CATEGORIES = ['tuition', 'registration', 'laboratory', 'other'];

    public function calculate(Program $program): array
    {
        $program->loadMissing('feeItems');

        $years = $program->duration_years !== null ? (float) $program->duration_years : null;
        $semesters = $years !== null ? (int) round($years * self::SEMESTERS_PER_YEAR) : null;
        $paidYears = $years !== null ? (int) ceil($years) : null;

        // البنود المعلّمة "unavailable" لا تدخل بالحساب وتُعدّ ناقصة
        $usable = $program->feeItems->filter(fn ($item) => $item->data_status !== 'unavailable');

        $lines = [];
        $total = 0.0;
        $computable = $years !== null && $usable->isNotEmpty();

        foreach ($usable as $item) {
            $times = match ($item->frequency) {
                'one_time' => 1,
                'per_semester' => $semesters,
                'per_year' => $paidYears,
                default => null,
            };

            $subtotal = $times !== null ? (float) $item->amount * $times : null;

            if ($subtotal === null) {
                $computable = false;
            } else {
                $total += $subtotal;
            }

            $lines[] = [
                'category' => $item->category,
                'label' => $item->label,
                'amount' => (float) $item->amount,
                'frequency' => $item->frequency,
                'times' => $times,
                'subtotal' => $subtotal,
                'data_status' => $item->data_status,
            ];
        }

        $finalTotal = $computable ? $total : null;

        // مساهمة كل فئة في الإجمالي
        $byCategory = [];
        foreach ($lines as $line) {
            if ($line['subtotal'] === null) {
                continue;
            }
            $byCategory[$line['category']] = ($byCategory[$line['category']] ?? 0) + $line['subtotal'];
        }

        $breakdown = [];
        foreach ($byCategory as $category => $subtotal) {
            $breakdown[] = [
                'category' => $category,
                'subtotal' => $subtotal,
                'percent' => ($finalTotal && $finalTotal > 0) ? round(($subtotal / $finalTotal) * 100, 1) : null,
            ];
        }

        $presentCategories = $usable->pluck('category')->unique()->all();
        $missing = array_values(array_diff(self::EXPECTED_CATEGORIES, $presentCategories));

        return [
            'years' => $years,
            'semesters' => $semesters,
            'currency' => $usable->first()?->currency ?? 'LYD',
            'lines' => $lines,
            'breakdown' => $breakdown,
            'total' => $finalTotal,
            'missing' => $missing,
            'is_complete' => $finalTotal !== null && empty($missing),
            'is_illustrative' => $usable->contains(fn ($item) => $item->data_status === 'synthetic'),
        ];
    }
}
