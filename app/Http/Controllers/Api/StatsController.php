<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    use ApiResponse;

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
                'value' => '20+',
                'icon' => 'experience-icon',
            ],
        ];

        return $this->success($stats);
    }
}
