<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Municipality;
use App\Models\Wilaya;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Seed the application's database with institutions.
     *
     * Run with: php artisan db:seed --class=InstitutionSeeder
     */
    public function run(): void
    {
        // Ensure we have Wilayas and Municipalities
        if (Wilaya::count() === 0) {
            $this->command->warn('No Wilayas found. Please run WilayaMunicipalitySeeder first.');

            return;
        }

        $this->command->info('Seeding institutions across wilayas...');

        // Major wilayas to seed institutions for
        $wilayaCodes = ['16', '31', '09', '25', '19', '06', '42', '05', '23', '15'];
        $types = ['primary', 'middle', 'high'];
        $typeNamesAr = [
            'primary' => 'مدرسة ابتدائية',
            'middle' => 'متوسطة',
            'high' => 'ثانوية',
        ];

        $counter = 0;

        foreach ($wilayaCodes as $code) {
            $wilaya = Wilaya::where('code', $code)->first();
            if (! $wilaya) {
                continue;
            }

            $municipalities = $wilaya->municipalities()->get();

            foreach ($municipalities as $municipality) {
                // Create 1-2 institutions per municipality
                $numInstitutions = rand(1, 2);

                for ($i = 1; $i <= $numInstitutions; $i++) {
                    $type = $types[array_rand($types)];
                    $typeName = match ($type) {
                        'primary' => 'Primary School',
                        'middle' => 'Middle School',
                        'high' => 'High School',
                    };

                    $name = $municipality->name.' '.$typeName.' '.$i;
                    $email = strtolower(str_replace([' ', "'", '-'], '', $municipality->name)).$i.'@school.dz';

                    if (Institution::where('email', $email)->exists()) {
                        continue;
                    }

                    Institution::create([
                        'name' => $name,
                        'name_ar' => $typeNamesAr[$type].' '.($municipality->name_ar ?? $municipality->name),
                        'wilaya_id' => $wilaya->id,
                        'municipality_id' => $municipality->id,
                        'address' => $municipality->name.', '.$wilaya->name,
                        'phone' => '0'.rand(21, 49).rand(100000, 999999),
                        'email' => $email,
                        'type' => $type,
                        'is_active' => true,
                    ]);
                    $counter++;
                }
            }
        }

        $this->command->info("✅ Created {$counter} institutions across wilayas!");
    }
}
