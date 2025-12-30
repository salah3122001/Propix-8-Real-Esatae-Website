<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get all site settings.
     */
    public function index()
    {
        $keys = [
            'home_hero_image',
            'site_name',
            'site_email',
            'site_logo',
            'site_phone',
            'site_address',
            'social_facebook',
            'social_instagram',
            'social_twitter',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::getValue($key);
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get a specific setting.
     */
    public function show($key)
    {
        $value = Setting::getValue($key);

        if ($value === null) {
            return response()->json([
                'success' => false,
                'message' => __('api.setting_not_found')
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                $key => $value
            ]
        ]);
    }
}
