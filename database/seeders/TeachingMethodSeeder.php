<?php

namespace Database\Seeders;

use App\Models\TeachingMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeachingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Lecture', 'name_ar' => 'محاضرة / إلقاء'],
            ['name' => 'Discussion', 'name_ar' => 'مناقشة'],
            ['name' => 'Group Work', 'name_ar' => 'عمل جماعي'],
            ['name' => 'Individual Work', 'name_ar' => 'عمل فردي'],
            ['name' => 'Brainstorming', 'name_ar' => 'عصف ذهني'],
            ['name' => 'Demonstration', 'name_ar' => 'عرض عملي'],
            ['name' => 'Case Study', 'name_ar' => 'دراسة حالة'],
            ['name' => 'Role Play', 'name_ar' => 'لعب الأدوار'],
            ['name' => 'Problem-Based Learning', 'name_ar' => 'التعلم القائم على المشكلات'],
            ['name' => 'Project-Based Learning', 'name_ar' => 'التعلم القائم على المشاريع'],
            ['name' => 'Inquiry-Based Learning', 'name_ar' => 'التعلم بالاستقصاء'],
            ['name' => 'Cooperative Learning', 'name_ar' => 'التعلم التعاوني'],
            ['name' => 'Flipped Classroom', 'name_ar' => 'الفصل المقلوب'],
            ['name' => 'Gamification', 'name_ar' => 'التلعيب'],
        ];

        foreach ($methods as $method) {
            TeachingMethod::firstOrCreate(['name' => $method['name']], $method);
        }
    }
}
