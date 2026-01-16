<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        Level::truncate();

        $levels = [
            // Middle School (from mock-locations)
            ['name' => '1AM', 'name_ar' => 'السنة الأولى متوسط'],
            ['name' => '2AM', 'name_ar' => 'السنة الثانية متوسط'],
            ['name' => '3AM', 'name_ar' => 'السنة الثالثة متوسط'],
            ['name' => '4AM', 'name_ar' => 'السنة الرابعة متوسط'],
            // Add some High School defaults just in case, or stick to mock?
            // Mock had commented out AS levels. I will add them as they are useful.
            ['name' => '1AS', 'name_ar' => 'السنة الأولى ثانوي'],
            ['name' => '2AS', 'name_ar' => 'السنة الثانية ثانوي'],
            ['name' => '3AS', 'name_ar' => 'السنة الثالثة ثانوي'],
        ];

        foreach ($levels as $level) {
            Level::firstOrCreate(['name' => $level['name']], $level);
        }
    }
}
