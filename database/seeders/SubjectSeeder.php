<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Mathematics', 'name_ar' => 'الرياضيات'],
            ['name' => 'Physics', 'name_ar' => 'الفيزياء'],
            ['name' => 'Science', 'name_ar' => 'العلوم الطبيعية'],
            ['name' => 'Arabic', 'name_ar' => 'اللغة العربية'],
            ['name' => 'French', 'name_ar' => 'اللغة الفرنسية'],
            ['name' => 'English', 'name_ar' => 'اللغة الإنجليزية'],
            ['name' => 'History & Geography', 'name_ar' => 'التاريخ والجغرافيا'],
            ['name' => 'Islamic Education', 'name_ar' => 'التربية الإسلامية'],
            ['name' => 'Drawing / Art', 'name_ar' => 'التربية التشكيلية'],
            ['name' => 'Computer Science', 'name_ar' => 'الإعلام الآلي'],
            ['name' => 'Civic Education', 'name_ar' => 'التربية المدنية'],
            ['name' => 'Sports', 'name_ar' => 'التربية البدنية'],
            ['name' => 'Music', 'name_ar' => 'التربية الموسيقية'],
             ['name' => 'Amazigh', 'name_ar' => 'اللغة الأمازيغية'],
        ];

        // Truncate table to restart IDs or just update/create
        Subject::truncate(); 
        
        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['name' => $subject['name']], $subject);
        }
    }
}
