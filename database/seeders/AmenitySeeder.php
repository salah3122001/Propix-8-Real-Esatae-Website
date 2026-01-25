<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villaImage = 'modern-luxury-house-with-swimming-pool.jpg';
        $villaSource = base_path('villa/' . $villaImage);
        $targetDir = 'amenities';

        if (File::exists($villaSource)) {
            Storage::disk('public')->makeDirectory($targetDir);
        }

        $amenities = [
            ['name_en' => 'Private Pool', 'name_ar' => 'حمام سباحة خاص'],
            ['name_en' => 'Shared Gym', 'name_ar' => 'صالة ألعاب رياضية مشتركة'],
            ['name_en' => 'Security', 'name_ar' => 'أمن وحراسة'],
            ['name_en' => 'Parking', 'name_ar' => 'موقف سيارات'],
            ['name_en' => 'Elevator', 'name_ar' => 'مصعد'],
            ['name_en' => 'Balcony', 'name_ar' => 'شرفة'],
            ['name_en' => 'Garden', 'name_ar' => 'حديقة'],
            ['name_en' => 'Central A/C', 'name_ar' => 'تكييف مركزي'],
            ['name_en' => 'Maid Service', 'name_ar' => 'خدمة تنظيف'],
            ['name_en' => 'Sea View', 'name_ar' => 'إطلالة على البحر'],
            ['name_en' => 'Kitchen Appliances', 'name_ar' => 'أجهزة مطبخ'],
            ['name_en' => 'Walk-in Closet', 'name_ar' => 'غرفة ملابس'],
            ['name_en' => 'Pets Allowed', 'name_ar' => 'مسموح بالحيوانات الأليفة'],
            ['name_en' => 'BBQ Area', 'name_ar' => 'منطقة شواء'],
            ['name_en' => 'Kids Play Area', 'name_ar' => 'منطقة ألعاب للأطفال'],
        ];

        foreach ($amenities as $index => $amenity) {
            $imageName = "amenity-" . ($index + 1) . ".jpg";
            if (File::exists($villaSource)) {
                File::copy($villaSource, Storage::disk('public')->path($targetDir . '/' . $imageName));
            }

            \App\Models\Amenity::updateOrCreate(
                ['name_en' => $amenity['name_en']],
                array_merge($amenity, ['icon' => $targetDir . '/' . $imageName])
            );
        }
    }
}
