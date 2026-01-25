<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\Unit;
use App\Models\UnitType;
use App\Models\User;
use App\Models\UnitMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $fakerAr = Faker::create('ar_EG');
        $fakerEn = Faker::create('en_US');
        $villaImage = 'modern-luxury-house-with-swimming-pool.jpg';
        $villaSource = base_path('villa/' . $villaImage);

        // 1. Create Cities
        $citiesData = [
            ['en' => 'Cairo', 'ar' => 'القاهرة'],
            ['en' => 'Giza', 'ar' => 'الجيزة'],
            ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
            ['en' => 'North Coast', 'ar' => 'الساحل الشمالي'],
            ['en' => 'Ain Sokhna', 'ar' => 'العين السخنة'],
            ['en' => 'New Capital', 'ar' => 'العاصمة الإدارية الجديدة'],
            ['en' => '6th of October', 'ar' => 'السادس من أكتوبر'],
            ['en' => 'Sheikh Zayed', 'ar' => 'الشيخ زايد'],
            ['en' => 'New Cairo', 'ar' => 'القاهرة الجديدة'],
            ['en' => 'Mansoura', 'ar' => 'المنصورة'],
        ];

        $cities = [];
        foreach ($citiesData as $city) {
            $cities[] = City::firstOrCreate(
                ['name_en' => $city['en']],
                ['name_ar' => $city['ar']]
            );
        }

        // 2. Create Unit Types
        $typesData = [
            ['en' => 'Apartment', 'ar' => 'شقة', 'icon' => 'apartment'],
            ['en' => 'Villa', 'ar' => 'فيلا', 'icon' => 'villa'],
            ['en' => 'Townhouse', 'ar' => 'تاون هاوس', 'icon' => 'home'],
            ['en' => 'Twin House', 'ar' => 'توين هاوس', 'icon' => 'holiday_village'],
            ['en' => 'Chalet', 'ar' => 'شاليه', 'icon' => 'beach_access'],
            ['en' => 'Duplex', 'ar' => 'دوبلكس', 'icon' => 'stairs'],
            ['en' => 'Penthouse', 'ar' => 'بنتهاوس', 'icon' => 'deck'],
            ['en' => 'Studio', 'ar' => 'ستوديو', 'icon' => 'weekend'],
            ['en' => 'Clinic', 'ar' => 'عيادة', 'icon' => 'medical_services'],
            ['en' => 'Office', 'ar' => 'مكتب', 'icon' => 'business_center'],
            ['en' => 'Shop', 'ar' => 'محلات', 'icon' => 'store'],
        ];

        $unitTypes = [];
        foreach ($typesData as $type) {
            if (File::exists($villaSource)) {
                Storage::disk('public')->makeDirectory('unit-types');
                File::copy($villaSource, Storage::disk('public')->path('unit-types/' . $villaImage));
            }

            $unitTypes[] = UnitType::firstOrCreate(
                ['name_en' => $type['en']],
                ['name_ar' => $type['ar'], 'icon' => 'unit-types/' . $villaImage]
            );
        }

        // 3. Create Users
        $usersToCreate = [
            [
                'email' => 'admin@admin.com',
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '01000000000',
                'status' => 'approved',
            ],
            [
                'email' => 'mohamed_ashraf4444@hotmail.com',
                'name' => 'Mohamed Ashraf',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '01000000001',
                'status' => 'approved',
            ],
            [
                'email' => 'mohamed_ashraf44444@hotmail.com',
                'name' => 'Mohamed Ashraf 2',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '01000000002',
                'status' => 'approved',
            ],
        ];

        $admin = null;
        foreach ($usersToCreate as $u) {
            $user = User::firstOrCreate(['email' => $u['email']], $u);

            // Assign super_admin role if not already assigned
            if ($u['role'] === 'admin' && !$user->hasRole('super_admin')) {
                $user->assignRole('super_admin');
            }

            if ($u['email'] === 'admin@admin.com') {
                $admin = $user;
            }
        }

        // 4. Create Developers
        $developersData = [
            ['name_en' => 'Emaar Misr', 'name_ar' => 'إعمار مصر'],
            ['name_en' => 'SODIC', 'name_ar' => 'سوديك'],
            ['name_en' => 'Palm Hills', 'name_ar' => 'بالم هيلز'],
            ['name_en' => 'Mountain View', 'name_ar' => 'ماونتن فيو'],
            ['name_en' => 'Orascom Development', 'name_ar' => 'أوراسكوم للتنمية'],
            ['name_en' => 'Talaat Moustafa Group (TMG)', 'name_ar' => 'مجموعة طلعت مصطفى'],
            ['name_en' => 'Hyde Park', 'name_ar' => 'هايد بارك'],
            ['name_en' => 'Tatweer Misr', 'name_ar' => 'تطوير مصر'],
            ['name_en' => 'Misr Italia', 'name_ar' => 'مصر إيطاليا'],
        ];

        $developers = [];
        foreach ($developersData as $devData) {
            if (File::exists($villaSource)) {
                Storage::disk('public')->makeDirectory('developers');
                File::copy($villaSource, Storage::disk('public')->path('developers/' . $villaImage));
            }

            $developers[] = Developer::firstOrCreate(
                ['name_en' => $devData['name_en']],
                [
                    'name_ar' => $devData['name_ar'],
                    'email' => str_replace(' ', '', strtolower($devData['name_en'])) . '@contact.com',
                    'phone' => $fakerAr->phoneNumber,
                    'address' => $fakerEn->address,
                    'status' => 'active',
                    'image' => 'developers/' . $villaImage
                ]
            );
        }

        // 5. Create Compounds
        $compoundsData = [
            ['name_en' => 'Marassi', 'name_ar' => 'مراسي', 'city' => 'North Coast', 'dev' => 'Emaar Misr'],
            ['name_en' => 'Mivida', 'name_ar' => 'ميفيدا', 'city' => 'New Cairo', 'dev' => 'Emaar Misr'],
            ['name_en' => 'Cairo Gate', 'name_ar' => 'كايرو جيت', 'city' => 'Sheikh Zayed', 'dev' => 'Emaar Misr'],
            ['name_en' => 'Villette', 'name_ar' => 'فيليت', 'city' => 'New Cairo', 'dev' => 'SODIC'],
            ['name_en' => 'SODIC West', 'name_ar' => 'سوديك ويست', 'city' => 'Sheikh Zayed', 'dev' => 'SODIC'],
            ['name_en' => 'Palm Hills Alexandria', 'name_ar' => 'بالم هيلز الإسكندرية', 'city' => 'Alexandria', 'dev' => 'Palm Hills'],
            ['name_en' => 'Badya', 'name_ar' => 'بادية', 'city' => '6th of October', 'dev' => 'Palm Hills'],
            ['name_en' => 'Mountain View iCity', 'name_ar' => 'ماونتن فيو آي سيتي', 'city' => 'New Cairo', 'dev' => 'Mountain View'],
            ['name_en' => 'Mountain View Chillout Park', 'name_ar' => 'ماونتن فيو تشيل أوت بارك', 'city' => '6th of October', 'dev' => 'Mountain View'],
            ['name_en' => 'Madinaty', 'name_ar' => 'مدينتي', 'city' => 'New Cairo', 'dev' => 'Talaat Moustafa Group (TMG)'],
            ['name_en' => 'Noor', 'name_ar' => 'نور', 'city' => 'New Capital', 'dev' => 'Talaat Moustafa Group (TMG)'],
            ['name_en' => 'IL Monte Galala', 'name_ar' => 'المونت جلالة', 'city' => 'Ain Sokhna', 'dev' => 'Tatweer Misr'],
            ['name_en' => 'Bloomfields', 'name_ar' => 'بلوم فيلدز', 'city' => 'New Cairo', 'dev' => 'Tatweer Misr'],
            ['name_en' => 'Taj City', 'name_ar' => 'تاج سيتي', 'city' => 'New Cairo', 'dev' => 'Misr Italia'],
        ];

        $compounds = [];
        foreach ($compoundsData as $comp) {
            $city = City::where('name_en', $comp['city'])->first();
            $dev = Developer::where('name_en', $comp['dev'])->first();

            if ($city && $dev) {
                $compounds[] = Compound::firstOrCreate(
                    ['name_en' => $comp['name_en']],
                    [
                        'name_ar' => $comp['name_ar'],
                        'description_en' => $fakerEn->paragraph,
                        'description_ar' => $fakerAr->paragraph,
                        'city_id' => $city->id,
                        'latitude' => $fakerEn->latitude(29, 31),
                        'longitude' => $fakerEn->longitude(30, 32),
                    ]
                );
            }
        }

        // 6. Create Units
        $offerTypes = ['sale', 'rent'];
        $devStatuses = ['ready', 'under_construction', 'handover_soon'];

        foreach ($compounds as $compound) {
            $numUnits = rand(3, 6);

            for ($k = 0; $k < $numUnits; $k++) {
                $seller = $admin;
                $type = $unitTypes[array_rand($unitTypes)];
                $dev = $developers[array_rand($developers)];

                $price = ($type->name_en == 'Villa' || $type->name_en == 'Twin House')
                        ? $fakerEn->numberBetween(5000000, 50000000)
                        : $fakerEn->numberBetween(1500000, 8000000);

                $selectedOfferType = $offerTypes[array_rand($offerTypes)];
                $isSale = ($selectedOfferType == 'sale');

                $unit = Unit::create([
                    'title_en' => $type->name_en . ' for ' . $selectedOfferType . ' in ' . $compound->name_en,
                    'title_ar' => $type->name_ar . ' للـ ' . ($isSale ? 'بيع' : 'إيجار') . ' في ' . $compound->name_ar,
                    'description_en' => $fakerEn->realText(200),
                    'description_ar' => $fakerAr->realText(200),
                    'price' => $price,
                    'price_per_m2' => $price / 100, // rough estimate
                    'offer_type' => $selectedOfferType,
                    'area' => $fakerEn->numberBetween(80, 500),
                    'rooms' => $fakerEn->numberBetween(1, 6),
                    'bathrooms' => $fakerEn->numberBetween(1, 4),
                    'garages' => $fakerEn->numberBetween(0, 2),
                    'build_year' => $fakerEn->year,
                    'status' => 'approved', // Active/Approved
                    'is_visible' => true,
                    'development_status' => $devStatuses[array_rand($devStatuses)],
                    'owner_id' => $seller->id,
                    'city_id' => $compound->city_id,
                    'unit_type_id' => $type->id,
                    'compound_id' => $compound->id,
                    'developer_id' => $dev->id,
                    'latitude' => $compound->latitude + (rand(-100, 100) / 10000), // Slight offset
                    'longitude' => $compound->longitude + (rand(-100, 100) / 10000),
                ]);
                if (File::exists($villaSource)) {
                    Storage::disk('public')->makeDirectory('units');
                    for ($m = 1; $m <= 3; $m++) {
                        $unitImageName = "unit-" . $unit->id . "-" . $m . ".jpg";
                        File::copy($villaSource, Storage::disk('public')->path('units/' . $unitImageName));
                        UnitMedia::create([
                            'unit_id' => $unit->id,
                            'type' => 'image',
                            'url' => 'units/' . $unitImageName,
                            'order' => $m,
                            'processing_status' => 'completed'
                        ]);
                    }

                    // Add Floorplan
                    $floorplanSource = base_path('images/floorplan.png');
                    if (File::exists($floorplanSource)) {
                        $floorplanName = "unit-" . $unit->id . "-floorplan.png";
                        File::copy($floorplanSource, Storage::disk('public')->path('units/' . $floorplanName));
                        UnitMedia::create([
                            'unit_id' => $unit->id,
                            'type' => 'floorplan',
                            'url' => 'units/' . $floorplanName,
                            'order' => 4,
                            'processing_status' => 'completed'
                        ]);
                    }
                }
            }
        }
    }
}
