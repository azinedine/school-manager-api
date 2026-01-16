<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            ['name' => 'Textbook', 'name_ar' => 'الكتاب المدرسي'],
            ['name' => 'Whiteboard', 'name_ar' => 'السبورة البيضاء'],
            ['name' => 'Blackboard', 'name_ar' => 'السبورة السوداء'],
            ['name' => 'Projector', 'name_ar' => 'جهاز العرض'],
            ['name' => 'Computer', 'name_ar' => 'الحاسوب'],
            ['name' => 'Tablet', 'name_ar' => 'جهاز لوحي'],
            ['name' => 'Interactive Board', 'name_ar' => 'السبورة التفاعلية'],
            ['name' => 'Worksheets', 'name_ar' => 'أوراق العمل'],
            ['name' => 'Flashcards', 'name_ar' => 'البطاقات التعليمية'],
            ['name' => 'Educational Videos', 'name_ar' => 'مقاطع فيديو تعليمية'],
            ['name' => 'Educational Software', 'name_ar' => 'برامج تعليمية'],
            ['name' => 'Lab Equipment', 'name_ar' => 'معدات المختبر'],
            ['name' => 'Maps', 'name_ar' => 'الخرائط'],
            ['name' => 'Charts', 'name_ar' => 'الرسوم البيانية'],
            ['name' => 'Models', 'name_ar' => 'النماذج'],
            ['name' => 'Manipulatives', 'name_ar' => 'الأدوات التعليمية'],
            ['name' => 'Calculators', 'name_ar' => 'الآلات الحاسبة'],
            ['name' => 'Rulers', 'name_ar' => 'المساطر'],
            ['name' => 'Geometric Tools', 'name_ar' => 'الأدوات الهندسية'],
            ['name' => 'Art Supplies', 'name_ar' => 'مستلزمات الرسم'],
            ['name' => 'Musical Instruments', 'name_ar' => 'الآلات الموسيقية'],
            ['name' => 'Sports Equipment', 'name_ar' => 'معدات رياضية'],
            ['name' => 'Notebooks', 'name_ar' => 'الدفاتر'],
            ['name' => 'Colored Pencils', 'name_ar' => 'أقلام ملونة'],
            ['name' => 'Markers', 'name_ar' => 'أقلام التحديد'],
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate(
                ['name' => $material['name']],
                $material
            );
        }
    }
}
