<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['name_en' => 'Private Pool', 'name_ar' => 'حمام سباحة خاص', 'icon' => 'pool_icon.png'],
            ['name_en' => 'Shared Gym', 'name_ar' => 'صالة ألعاب رياضية مشتركة', 'icon' => 'gym_icon.png'],
            ['name_en' => 'Security', 'name_ar' => 'أمن وحراسة', 'icon' => 'security_icon.png'],
            ['name_en' => 'Parking', 'name_ar' => 'موقف سيارات', 'icon' => 'parking_icon.png'],
            ['name_en' => 'Elevator', 'name_ar' => 'مصعد', 'icon' => 'elevator_icon.png'],
            ['name_en' => 'Balcony', 'name_ar' => 'شرفة', 'icon' => 'balcony_icon.png'],
            ['name_en' => 'Garden', 'name_ar' => 'حديقة', 'icon' => 'garden_icon.png'],
            ['name_en' => 'Central A/C', 'name_ar' => 'تكييف مركزي', 'icon' => 'ac_icon.png'],
            ['name_en' => 'Maid Service', 'name_ar' => 'خدمة تنظيف', 'icon' => 'maid_icon.png'],
            ['name_en' => 'Sea View', 'name_ar' => 'إطلالة على البحر', 'icon' => 'sea_view_icon.png'],
            ['name_en' => 'Kitchen Appliances', 'name_ar' => 'أجهزة مطبخ', 'icon' => 'kitchen_icon.png'],
            ['name_en' => 'Walk-in Closet', 'name_ar' => 'غرفة ملابس', 'icon' => 'closet_icon.png'],
            ['name_en' => 'Pets Allowed', 'name_ar' => 'مسموح بالحيوانات الأليفة', 'icon' => 'pets_icon.png'],
            ['name_en' => 'BBQ Area', 'name_ar' => 'منطقة شواء', 'icon' => 'bbq_icon.png'],
            ['name_en' => 'Kids Play Area', 'name_ar' => 'منطقة ألعاب للأطفال', 'icon' => 'kids_icon.png'],
        ];

        foreach ($amenities as $amenity) {
            \App\Models\Amenity::updateOrCreate(
                ['name_en' => $amenity['name_en']], // Avoid duplicates based on English name
                $amenity
            );
        }
    }
}
