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

        $algiers = Wilaya::where('code', '16')->first();
        $oran = Wilaya::where('code', '31')->first();
        
        // Ensure specific municipalities exist or pick random ones from the wilaya
        $algiersMuni = $algiers ? $algiers->municipalities()->first() : Municipality::first();
        $oranMuni = $oran ? $oran->municipalities()->first() : Municipality::skip(1)->first();

        if (!$algiersMuni || !$oranMuni) {
             $this->command->warn('Not enough municipalities found to seed institutions properly.');
             return;
        }

        $institutions = [
            [
                'name' => 'El Amel High School',
                'name_ar' => 'ثانوية الأمل',
                'wilaya_id' => $algiersMuni->wilaya_id,
                'municipality_id' => $algiersMuni->id,
                'address' => '123 Main St, Algiers Centre',
                'phone' => '021234567',
                'email' => 'contact@elamel-school.dz',
                'type' => 'high',
                'is_active' => true,
            ],
            [
                'name' => 'Al Nahda Middle School',
                'name_ar' => 'متوسطة النهضة',
                'wilaya_id' => $oranMuni->wilaya_id,
                'municipality_id' => $oranMuni->id,
                'address' => '456 Freedom Ave, Oran',
                'phone' => '041234567',
                'email' => 'info@nahda-school.dz',
                'type' => 'middle',
                'is_active' => true,
            ],
            [
                'name' => 'Future Primary School',
                'name_ar' => 'مدرسة المستقبل الابتدائية',
                'wilaya_id' => $algiersMuni->wilaya_id,
                'municipality_id' => $algiersMuni->id,
                'address' => '789 Knowledge Ln, Hydra',
                'phone' => '021987654',
                'email' => 'hello@future-school.dz',
                'type' => 'primary',
                'is_active' => true,
            ],
        ];

        foreach ($institutions as $data) {
            if (Institution::where('email', $data['email'])->exists()) {
                $this->command->info("Institution {$data['name']} already exists. Skipping.");
                continue;
            }

            Institution::create($data);
            $this->command->info("✅ Created Institution: {$data['name']}");
        }
    }
}
