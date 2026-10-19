<?php

namespace App\Services;

/**
 * تحليل فرق التكلفة بين برنامجين اعتمادًا على ناتج CostCalculator::calculate().
 *
 * المبدأ: يوضّح مصدر الفرق الحسابي فقط (أي فئة رسوم تسبّبت به)، ولا يعني أن البرنامج
 * الأغلى أفضل جودة. والفئات المتوفرة في أحد البرنامجين فقط لا تدخل في الفرق ولا تُعامل كصفر.
 */
class CostComparison
{
    /**
     * @param  array  $base   ناتج calculate() للبرنامج الأساس
     * @param  array  $other  ناتج calculate() للبرنامج المقارَن به
     */
    public function analyze(array $base, array $other): array
    {
        if ($base['currency'] !== $other['currency']) {
            return [
                'comparable' => false,
                'reason' => 'عملة الرسوم مختلفة بين البرنامجين، لذلك لا يمكن حساب الفرق.',
            ];
        }

        $baseByCategory = collect($base['breakdown'])->pluck('subtotal', 'category');
        $otherByCategory = collect($other['breakdown'])->pluck('subtotal', 'category');

        $rows = [];
        $comparableDiff = 0.0;
        $skipped = [];

        foreach (CostCalculator::EXPECTED_CATEGORIES as $category) {
            $baseAmount = $baseByCategory->get($category);
            $otherAmount = $otherByCategory->get($category);

            // فئة غير متوفرة في أي من البرنامجين: لا تظهر في التحليل
            if ($baseAmount === null && $otherAmount === null) {
                continue;
            }

            // فئة متوفرة في أحدهما فقط: تظهر لكن لا تدخل في الفرق
            if ($baseAmount === null || $otherAmount === null) {
                $skipped[] = $category;
                $rows[] = [
                    'category' => $category,
                    'base' => $baseAmount,
                    'other' => $otherAmount,
                    'diff' => null,
                    'share' => null,
                ];
                continue;
            }

            $diff = round($otherAmount - $baseAmount, 2);
            $comparableDiff += $diff;

            $rows[] = [
                'category' => $category,
                'base' => $baseAmount,
                'other' => $otherAmount,
                'diff' => $diff,
                'share' => null,
            ];
        }

        // نسبة مساهمة الفئة = فرق الفئة ÷ إجمالي الفرق (قد تتجاوز 100% أو تكون سالبة عند تعويض الفئات لبعضها)
        if (abs($comparableDiff) > 0.0001) {
            foreach ($rows as &$row) {
                if ($row['diff'] !== null) {
                    // "+ 0.0" تحوّل -0.0 إلى 0.0 حتى لا تظهر "‎-0%" لفئة فرقها صفر
                    $row['share'] = round($row['diff'] / $comparableDiff * 100, 1) + 0.0;
                }
            }
            unset($row);
        }

        return [
            'comparable' => true,
            'rows' => $rows,
            'comparable_diff' => round($comparableDiff, 2),
            'skipped' => $skipped,
            'has_comparable' => collect($rows)->contains(fn ($row) => $row['diff'] !== null),
            // الفرق موثوق فقط إذا كان إجمالي البرنامجين مكتملًا
            'is_reliable' => $base['is_complete'] && $other['is_complete'],
        ];
    }
}
