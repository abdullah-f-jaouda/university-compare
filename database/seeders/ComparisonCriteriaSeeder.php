<?php

namespace Database\Seeders;

use App\Models\ComparisonCriterion;
use Illuminate\Database\Seeder;

class ComparisonCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'key' => 'affordability',
                'name' => 'القدرة على تحمّل التكلفة',
                'description' => 'التكلفة الإجمالية التقديرية للبرنامج — الأقل أفضل.',
                'direction' => 'lower_is_better',
                'default_weight' => 5,
            ],
            [
                'key' => 'duration',
                'name' => 'مدة الدراسة',
                'description' => 'عدد سنوات الدراسة — الأقصر أفضل.',
                'direction' => 'lower_is_better',
                'default_weight' => 3,
            ],
            [
                'key' => 'practical_ratio',
                'name' => 'نسبة الجانب العملي',
                'description' => 'نسبة المقررات/الساعات العملية في البرنامج — الأعلى أفضل.',
                'direction' => 'higher_is_better',
                'default_weight' => 4,
            ],
            [
                'key' => 'facilities',
                'name' => 'المرافق والخدمات المتاحة',
                'description' => 'عدد المرافق والخدمات الموثّقة والمتاحة في الجامعة — الأعلى أفضل.',
                'direction' => 'higher_is_better',
                'default_weight' => 2,
            ],
        ];

        foreach ($criteria as $criterion) {
            ComparisonCriterion::updateOrCreate(
                ['key' => $criterion['key']],
                $criterion + ['is_active' => true]
            );
        }

        $this->command->info('تم زرع معايير المقارنة (4 معايير).');
    }
}
