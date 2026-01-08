<?php

namespace Database\Seeders;

use App\Models\Reference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $references = [
            ['name' => 'Textbook', 'name_ar' => 'الكتاب المدرسي'],
            ['name' => 'Curriculum & Plans', 'name_ar' => 'المنهاج و المخططات'],
            ['name' => 'Teacher\'s Prior Knowledge', 'name_ar' => 'معارف قبلية للأستاذ'],
            ['name' => 'Internet', 'name_ar' => 'الأنترنت'],
            ['name' => 'Reference Books', 'name_ar' => 'المراجع و الكتب'],
            ['name' => 'Academic Articles', 'name_ar' => 'مقالات أكاديمية'],
            ['name' => 'Previous Exams', 'name_ar' => 'امتحانات سابقة'],
            ['name' => 'Educational Digital Resources', 'name_ar' => 'الموارد الرقمية التربوية'],
            ['name' => 'Teacher Guide', 'name_ar' => 'دليل المعلم'],
            ['name' => 'Dictionary', 'name_ar' => 'القاموس'],
            ['name' => 'Encyclopedia', 'name_ar' => 'الموسوعة'],
            ['name' => 'Official Documents', 'name_ar' => 'الوثائق الرسمية'],
        ];

        foreach ($references as $reference) {
            Reference::firstOrCreate(
                ['name' => $reference['name']],
                $reference
            );
        }
    }
}
