<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        $stats = [
            [
                'label' => app()->getLocale() === 'ar' ? 'مشاريع عقارية' : 'Real Estate Projects',
                'value' => \App\Models\Compound::count() . '+',
                'icon' => 'projects-icon',
            ],
            [
                'label' => app()->getLocale() === 'ar' ? 'وحدات تم تأجيرها' : 'Rented Units',
                'value' => \App\Models\Unit::where('status', 'rented')->count() . '+',
                'icon' => 'rented-icon',
            ],
            [
                'label' => app()->getLocale() === 'ar' ? 'عملاء سعداء' : 'Happy Clients',
                'value' => \App\Models\User::count() . '+',
                'icon' => 'clients-icon',
            ],
            [
                'label' => app()->getLocale() === 'ar' ? 'سنوات من الخبرة' : 'Years of Experience',
                'value' => '20+', // Fixed value as per usual business logic or could be from settings
                'icon' => 'experience-icon',
            ],
        ];

        return response()->json([
            'status' => true,
            'data' => $stats
        ]);
    }
}
