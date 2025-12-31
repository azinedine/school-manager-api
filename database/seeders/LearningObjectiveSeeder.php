<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningObjectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $objectives = [
            ['name' => 'Recall facts', 'name_ar' => 'تذكر الحقائق'],
            ['name' => 'Understand concepts', 'name_ar' => 'فهم المفاهيم'],
            ['name' => 'Apply knowledge', 'name_ar' => 'تطبيق المعرفة'],
            ['name' => 'Analyze information', 'name_ar' => 'تحليل المعلومات'],
            ['name' => 'Evaluate outcomes', 'name_ar' => 'تقييم النتائج'],
            ['name' => 'Create new work', 'name_ar' => 'إنشاء عمل جديد'],
            ['name' => 'Define terms', 'name_ar' => 'تعريف المصطلحات'],
            ['name' => 'Describe processes', 'name_ar' => 'وصف العمليات'],
            ['name' => 'Identify components', 'name_ar' => 'تحديد المكونات'],
            ['name' => 'Calculate results', 'name_ar' => 'حساب النتائج'],
            ['name' => 'Demonstrate skills', 'name_ar' => 'إظهار المهارات'],
            ['name' => 'Compare and contrast', 'name_ar' => 'المقارنة والمقابلة'],
            ['name' => 'Solve problems', 'name_ar' => 'حل المشكلات'],
            ['name' => 'Interpret data', 'name_ar' => 'تفسير البيانات'],
            ['name' => 'Formulate hypotheses', 'name_ar' => 'صياغة الفرضيات'],
        ];

        foreach ($objectives as $objective) {
            \App\Models\LearningObjective::create($objective);
        }
    }
}
