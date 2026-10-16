<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\FeeItem;
use App\Models\Facility;
use App\Models\Program;
use App\Models\University;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * بيانات تجريبية (synthetic) لأغراض العرض والاختبار فقط.
     * أسماء الجامعات حقيقية (حسب الوثيقة المعتمدة) لكن كل الأرقام والتفاصيل وهمية
     * ومعلّمة data_status = synthetic، ولا تُعرض كمعلومات رسمية.
     */
    public function run(): void
    {
        if (University::count() > 0) {
            $this->command->warn('الجامعات موجودة مسبقًا — تم تخطي CatalogSeeder لتجنب التكرار.');
            return;
        }

        $note = 'بيانات تجريبية لأغراض العرض والاختبار — ليست بيانات رسمية.';

        // نموذج المقررات لكل مجال: [الاسم، الساعات، الفصل، النوع]
        $coursesByDiscipline = [
            'هندسة البرمجيات' => [
                ['مقدمة في البرمجة', 3, 1, 'mixed'],
                ['هياكل البيانات', 3, 3, 'mixed'],
                ['هندسة المتطلبات', 3, 5, 'theoretical'],
                ['اختبار وضمان جودة البرمجيات', 3, 7, 'practical'],
            ],
            'علوم الحاسوب' => [
                ['أساسيات البرمجة', 3, 1, 'mixed'],
                ['الخوارزميات', 3, 4, 'theoretical'],
                ['أنظمة التشغيل', 3, 5, 'mixed'],
                ['الذكاء الاصطناعي (مدخل)', 3, 7, 'theoretical'],
            ],
            'تقنية المعلومات' => [
                ['مدخل إلى تقنية المعلومات', 3, 1, 'theoretical'],
                ['شبكات الحاسوب', 3, 3, 'practical'],
                ['إدارة قواعد البيانات', 3, 5, 'practical'],
                ['إدارة الخوادم', 3, 7, 'practical'],
            ],
            'الأمن السيبراني' => [
                ['مبادئ أمن المعلومات', 3, 2, 'theoretical'],
                ['أمن الشبكات', 3, 4, 'practical'],
                ['التشفير', 3, 5, 'theoretical'],
                ['اختبار الاختراق (مدخل)', 3, 7, 'practical'],
            ],
            'نظم المعلومات' => [
                ['مدخل إلى نظم المعلومات', 3, 1, 'theoretical'],
                ['تحليل وتصميم النظم', 3, 4, 'mixed'],
                ['قواعد البيانات', 3, 5, 'mixed'],
                ['ذكاء الأعمال', 3, 7, 'theoretical'],
            ],
        ];

        // الجامعات + برامجها + بنود الرسوم (بالدينار الليبي LYD)
        // fees: tuition (لكل فصل) / registration (مرة واحدة) / lab (لكل فصل) / other (لكل سنة)
        // قيمة null تعني أن البند غير متوفر ولا يُحسب كصفر
        $catalog = [
            [
                'name' => 'الجامعة الليبية الدولية',
                'facilities' => [
                    ['facility', 'معامل حاسوب', true],
                    ['facility', 'مكتبة', true],
                    ['service', 'الإرشاد الأكاديمي', true],
                ],
                'programs' => [
                    ['هندسة البرمجيات', 'bachelor', 4.0, 140, 40, 70, ['tuition' => 2500, 'registration' => 300, 'lab' => 400, 'other' => 200]],
                    ['علوم الحاسوب', 'bachelor', 4.0, 136, 35, 70, ['tuition' => 2300, 'registration' => 300, 'lab' => 350, 'other' => 200]],
                ],
            ],
            [
                'name' => 'جامعة بنغازي الأهلية',
                'facilities' => [
                    ['facility', 'معامل حاسوب', true],
                    ['facility', 'قاعة مؤتمرات', true],
                    ['service', 'النقل الجامعي', false],
                ],
                'programs' => [
                    ['تقنية المعلومات', 'bachelor', 4.0, 132, 45, 65, ['tuition' => 2000, 'registration' => 250, 'lab' => 300, 'other' => null]],
                    ['الأمن السيبراني', 'bachelor', 4.0, 138, 50, 70, ['tuition' => 2600, 'registration' => 250, 'lab' => 450, 'other' => 150]],
                ],
            ],
            [
                'name' => 'جامعة بنغازي الحديثة',
                'facilities' => [
                    ['facility', 'مكتبة', true],
                    ['service', 'الإرشاد الأكاديمي', true],
                ],
                'programs' => [
                    // رسوم المعامل غير متوفرة عمدًا لاختبار معالجة البيانات الناقصة
                    ['هندسة البرمجيات', 'bachelor', 4.5, 144, 30, 68, ['tuition' => 1800, 'registration' => 200, 'lab' => null, 'other' => 100]],
                    // نسبة الجانب العملي غير متوفرة عمدًا
                    ['نظم المعلومات', 'bachelor', 4.0, 130, null, 65, ['tuition' => 1700, 'registration' => 200, 'lab' => 250, 'other' => 100]],
                ],
            ],
            [
                'name' => 'جامعة السلام الدولية',
                'facilities' => [
                    ['facility', 'معامل حاسوب', true],
                    ['facility', 'مكتبة', true],
                    ['service', 'النقل الجامعي', true],
                    ['service', 'التدريب الصيفي', true],
                ],
                'programs' => [
                    ['علوم الحاسوب', 'bachelor', 4.0, 134, 38, 72, ['tuition' => 2200, 'registration' => 350, 'lab' => 380, 'other' => 250]],
                    ['تقنية المعلومات', 'diploma', 3.0, 96, 55, 60, ['tuition' => 1500, 'registration' => 200, 'lab' => 300, 'other' => 100]],
                ],
            ],
        ];

        foreach ($catalog as $uniData) {
            $university = University::create([
                'name' => $uniData['name'],
                'city' => 'بنغازي',
                'description' => 'جامعة خاصة في بنغازي — بيانات تجريبية.',
                'is_active' => true,
                'data_status' => 'synthetic',
                'source_note' => $note,
            ]);

            foreach ($uniData['facilities'] as [$type, $name, $available]) {
                Facility::create([
                    'university_id' => $university->id,
                    'type' => $type,
                    'name' => $name,
                    'is_available' => $available,
                    'data_status' => 'synthetic',
                    'source_note' => $note,
                ]);
            }

            foreach ($uniData['programs'] as [$name, $degree, $years, $credits, $practical, $minPct, $fees]) {
                $program = Program::create([
                    'university_id' => $university->id,
                    'name' => $name,
                    'discipline' => $name,
                    'degree_type' => $degree,
                    'duration_years' => $years,
                    'total_credit_hours' => $credits,
                    'practical_percentage' => $practical,
                    'min_admission_percentage' => $minPct,
                    'admission_notes' => 'متطلبات قبول تجريبية لأغراض العرض.',
                    'description' => "برنامج {$name} — بيانات تجريبية.",
                    'is_active' => true,
                    'data_status' => 'synthetic',
                    'source_note' => $note,
                ]);

                // بنود الرسوم: تُنشأ فقط للبنود المتوفرة
                $feeDefinitions = [
                    'tuition' => ['tuition', 'رسوم دراسية', 'per_semester'],
                    'registration' => ['registration', 'رسوم تسجيل', 'one_time'],
                    'lab' => ['laboratory', 'رسوم معامل', 'per_semester'],
                    'other' => ['other', 'رسوم أخرى إلزامية', 'per_year'],
                ];

                foreach ($feeDefinitions as $key => [$category, $label, $frequency]) {
                    if ($fees[$key] === null) {
                        continue;
                    }

                    FeeItem::create([
                        'program_id' => $program->id,
                        'category' => $category,
                        'label' => $label,
                        'amount' => $fees[$key],
                        'currency' => 'LYD',
                        'frequency' => $frequency,
                        'academic_year' => '2026/2027',
                        'data_status' => 'synthetic',
                        'source_note' => $note,
                    ]);
                }

                foreach ($coursesByDiscipline[$name] ?? [] as [$courseName, $hours, $semester, $type]) {
                    Course::create([
                        'program_id' => $program->id,
                        'name' => $courseName,
                        'credit_hours' => $hours,
                        'semester' => $semester,
                        'course_type' => $type,
                        'data_status' => 'synthetic',
                    ]);
                }
            }
        }

        $this->command->info('تم زرع 4 جامعات و8 برامج مع الرسوم والمقررات والمرافق (كلها synthetic).');
    }
}
