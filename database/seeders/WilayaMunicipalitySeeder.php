<?php

namespace Database\Seeders;

use App\Models\Municipality;
use App\Models\Wilaya;
use Illuminate\Database\Seeder;

class WilayaMunicipalitySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Wilayas and Municipalities...');

        $data = [
            ['code' => '01', 'name' => 'Adrar', 'name_ar' => 'أدرار', 'municipalities' => ['Adrar', 'Reggane', 'Timimoun']],
            ['code' => '02', 'name' => 'Chlef', 'name_ar' => 'الشلف', 'municipalities' => ['Chlef', 'Ténès', 'El Karimia']],
            ['code' => '03', 'name' => 'Laghouat', 'name_ar' => 'الأغواط', 'municipalities' => ['Laghouat', 'Aflou', 'Ksar El Hirane']],
            ['code' => '04', 'name' => 'Oum El Bouaghi', 'name_ar' => 'أم البواقي', 'municipalities' => ['Oum El Bouaghi', 'Ain Beida', 'Ain Mlila']],
            ['code' => '05', 'name' => 'Batna', 'name_ar' => 'باتنة', 'municipalities' => ['Batna', 'Barika', 'Arris']],
            ['code' => '06', 'name' => 'Béjaïa', 'name_ar' => 'بجاية', 'municipalities' => ['Béjaïa', 'Akbou', 'Kherrata']],
            ['code' => '07', 'name' => 'Biskra', 'name_ar' => 'بسكرة', 'municipalities' => ['Biskra', 'Tolga', 'Ouled Djellal']],
            ['code' => '08', 'name' => 'Béchar', 'name_ar' => 'بشار', 'municipalities' => ['Béchar', 'Kenadsa', 'Abadla']],
            ['code' => '09', 'name' => 'Blida', 'name_ar' => 'البليدة', 'municipalities' => ['Blida', 'Boufarik', 'El Affroun']],
            ['code' => '10', 'name' => 'Bouira', 'name_ar' => 'البويرة', 'municipalities' => ['Bouira', 'Lakhdaria', 'Sour El Ghozlane']],
            ['code' => '11', 'name' => 'Tamanrasset', 'name_ar' => 'تمنراست', 'municipalities' => ['Tamanrasset', 'In Salah', 'Abalessa']],
            ['code' => '12', 'name' => 'Tébessa', 'name_ar' => 'تبسة', 'municipalities' => ['Tébessa', 'Bir El Ater', 'Cheria']],
            ['code' => '13', 'name' => 'Tlemcen', 'name_ar' => 'تلمسان', 'municipalities' => ['Tlemcen', 'Maghnia', 'Remchi']],
            ['code' => '14', 'name' => 'Tiaret', 'name_ar' => 'تيارت', 'municipalities' => ['Tiaret', 'Frenda', 'Sougueur']],
            ['code' => '15', 'name' => 'Tizi Ouzou', 'name_ar' => 'تيزي وزو', 'municipalities' => ['Tizi Ouzou', 'Azazga', 'Draa El Mizan']],
            ['code' => '16', 'name' => 'Alger', 'name_ar' => 'الجزائر', 'municipalities' => ['Alger Centre', 'Bab El Oued', 'Hussein Dey', 'El Harrach', 'Bir Mourad Rais', 'Hydra', 'Kouba', 'Sidi M\'hamed']],
            ['code' => '17', 'name' => 'Djelfa', 'name_ar' => 'الجلفة', 'municipalities' => ['Djelfa', 'Messaad', 'Ain Oussera']],
            ['code' => '18', 'name' => 'Jijel', 'name_ar' => 'جيجل', 'municipalities' => ['Jijel', 'El Milia', 'Taher']],
            ['code' => '19', 'name' => 'Sétif', 'name_ar' => 'سطيف', 'municipalities' => ['Sétif', 'El Eulma', 'Ain Oulmene']],
            ['code' => '20', 'name' => 'Saïda', 'name_ar' => 'سعيدة', 'municipalities' => ['Saïda', 'Ain El Hadjar', 'El Hassasna']],
            ['code' => '21', 'name' => 'Skikda', 'name_ar' => 'سكيكدة', 'municipalities' => ['Skikda', 'Collo', 'Azzaba']],
            ['code' => '22', 'name' => 'Sidi Bel Abbès', 'name_ar' => 'سيدي بلعباس', 'municipalities' => ['Sidi Bel Abbès', 'Telagh', 'Sfisef']],
            ['code' => '23', 'name' => 'Annaba', 'name_ar' => 'عنابة', 'municipalities' => ['Annaba', 'El Bouni', 'El Hadjar']],
            ['code' => '24', 'name' => 'Guelma', 'name_ar' => 'قالمة', 'municipalities' => ['Guelma', 'Bouchegouf', 'Oued Zenati']],
            ['code' => '25', 'name' => 'Constantine', 'name_ar' => 'قسنطينة', 'municipalities' => ['Constantine', 'El Khroub', 'Hamma Bouziane']],
            ['code' => '26', 'name' => 'Médéa', 'name_ar' => 'المدية', 'municipalities' => ['Médéa', 'Berrouaghia', 'Ksar El Boukhari']],
            ['code' => '27', 'name' => 'Mostaganem', 'name_ar' => 'مستغانم', 'municipalities' => ['Mostaganem', 'Ain Tedeles', 'Sidi Ali']],
            ['code' => '28', 'name' => 'M\'Sila', 'name_ar' => 'المسيلة', 'municipalities' => ['M\'Sila', 'Bou Saada', 'Ain El Melh']],
            ['code' => '29', 'name' => 'Mascara', 'name_ar' => 'معسكر', 'municipalities' => ['Mascara', 'Sig', 'Tighennif']],
            ['code' => '30', 'name' => 'Ouargla', 'name_ar' => 'ورقلة', 'municipalities' => ['Ouargla', 'Hassi Messaoud', 'Touggourt']],
            ['code' => '31', 'name' => 'Oran', 'name_ar' => 'وهران', 'municipalities' => ['Oran', 'Bir El Djir', 'Es Senia', 'Ain El Turk', 'Arzew']],
            ['code' => '32', 'name' => 'El Bayadh', 'name_ar' => 'البيض', 'municipalities' => ['El Bayadh', 'Bougtob', 'Brezina']],
            ['code' => '33', 'name' => 'Illizi', 'name_ar' => 'إليزي', 'municipalities' => ['Illizi', 'Djanet', 'In Amenas']],
            ['code' => '34', 'name' => 'Bordj Bou Arreridj', 'name_ar' => 'برج بوعريريج', 'municipalities' => ['Bordj Bou Arreridj', 'Ras El Oued', 'Bordj Ghedir']],
            ['code' => '35', 'name' => 'Boumerdès', 'name_ar' => 'بومرداس', 'municipalities' => ['Boumerdès', 'Dellys', 'Bordj Menaiel']],
            ['code' => '36', 'name' => 'El Tarf', 'name_ar' => 'الطارف', 'municipalities' => ['El Tarf', 'El Kala', 'Bouteldja']],
            ['code' => '37', 'name' => 'Tindouf', 'name_ar' => 'تندوف', 'municipalities' => ['Tindouf']],
            ['code' => '38', 'name' => 'Tissemsilt', 'name_ar' => 'تيسمسيلت', 'municipalities' => ['Tissemsilt', 'Bordj Bounama', 'Lardjem']],
            ['code' => '39', 'name' => 'El Oued', 'name_ar' => 'الوادي', 'municipalities' => ['El Oued', 'Guemar', 'Debila']],
            ['code' => '40', 'name' => 'Khenchela', 'name_ar' => 'خنشلة', 'municipalities' => ['Khenchela', 'Kais', 'Chechar']],
            ['code' => '41', 'name' => 'Souk Ahras', 'name_ar' => 'سوق أهراس', 'municipalities' => ['Souk Ahras', 'Sedrata', 'Mechroha']],
            ['code' => '42', 'name' => 'Tipaza', 'name_ar' => 'تيبازة', 'municipalities' => ['Tipaza', 'Cherchell', 'Hadjout', 'Kolea']],
            ['code' => '43', 'name' => 'Mila', 'name_ar' => 'ميلة', 'municipalities' => ['Mila', 'Chelghoum Laid', 'Ferdjioua']],
            ['code' => '44', 'name' => 'Aïn Defla', 'name_ar' => 'عين الدفلى', 'municipalities' => ['Aïn Defla', 'Khemis Miliana', 'El Attaf']],
            ['code' => '45', 'name' => 'Naâma', 'name_ar' => 'النعامة', 'municipalities' => ['Naâma', 'Mecheria', 'Ain Sefra']],
            ['code' => '46', 'name' => 'Aïn Témouchent', 'name_ar' => 'عين تموشنت', 'municipalities' => ['Aïn Témouchent', 'Beni Saf', 'El Malah']],
            ['code' => '47', 'name' => 'Ghardaïa', 'name_ar' => 'غرداية', 'municipalities' => ['Ghardaïa', 'Metlili', 'El Meniaa']],
            ['code' => '48', 'name' => 'Relizane', 'name_ar' => 'غليزان', 'municipalities' => ['Relizane', 'Oued Rhiou', 'Mazouna']],
        ];

        foreach ($data as $item) {
            $wilaya = Wilaya::create([
                'code' => $item['code'],
                'name' => $item['name'],
                'name_ar' => $item['name_ar'],
            ]);

            foreach ($item['municipalities'] as $municipalityName) {
                Municipality::create([
                    'wilaya_id' => $wilaya->id,
                    'name' => $municipalityName,
                    'name_ar' => null,
                ]);
            }
        }

        $this->command->info('✅ Seeded 48 Wilayas with municipalities!');
    }
}
