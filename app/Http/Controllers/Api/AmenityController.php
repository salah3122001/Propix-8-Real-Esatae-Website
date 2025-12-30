<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = \App\Models\Amenity::all();
        if ($amenities->isEmpty()) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }
        return \App\Http\Resources\AmenityResource::collection($amenities);
    }
}
