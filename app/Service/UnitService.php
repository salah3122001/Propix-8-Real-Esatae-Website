<?php

namespace App\Service;

use App\Models\Unit;
use Illuminate\Pagination\LengthAwarePaginator;

class UnitService
{
    public function getFilteredUnits(array $filters, $perPage = 10): LengthAwarePaginator
    {
        $query = Unit::query()
            ->with(['owner', 'city', 'compound', 'developer', 'type', 'media', 'amenities'])
            ->where('status', 'approved') // Only show approved units for public
            ->where('is_visible', true);

        // Keyword Search
        if (isset($filters['q']) && !empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('title_ar', 'like', "%{$q}%")
                    ->orWhere('title_en', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhere('description_ar', 'like', "%{$q}%")
                    ->orWhere('description_en', 'like', "%{$q}%");
            });
        }

        if (isset($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (isset($filters['compound_id'])) {
            $query->where('compound_id', $filters['compound_id']);
        }

        if (isset($filters['offer_type'])) {
            $query->where('offer_type', $filters['offer_type']);
        }

        if (isset($filters['unit_type_id'])) {
            $query->where('unit_type_id', $filters['unit_type_id']);
        }

        if (isset($filters['unit_type'])) {
            $unitType = $filters['unit_type'];
            if (is_numeric($unitType)) {
                $query->where('unit_type_id', $unitType);
            } else {
                $query->whereHas('type', function ($q) use ($unitType) {
                    $q->where('name_ar', 'like', "%{$unitType}%")
                        ->orWhere('name_en', 'like', "%{$unitType}%");
                });
            }
        }

        // Price Range
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Rooms & Bathrooms
        if (isset($filters['rooms'])) {
            $query->where('rooms', '>=', $filters['rooms']);
        }

        if (isset($filters['bathrooms'])) {
            $query->where('bathrooms', '>=', $filters['bathrooms']);
        }

        // Area Range
        if (isset($filters['min_area'])) {
            $query->where('area', '>=', $filters['min_area']);
        }

        if (isset($filters['max_area'])) {
            $query->where('area', '<=', $filters['max_area']);
        }

        // Amenities Filter
        if (isset($filters['amenities']) && is_array($filters['amenities'])) {
            $query->whereHas('amenities', function ($q) use ($filters) {
                $q->whereIn('amenities.id', $filters['amenities']);
            });
        }

        // Development Status Filter
        if (isset($filters['development_status'])) {
            $query->where('development_status', $filters['development_status']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    public function getUnitById(int $id): Unit
    {
        return Unit::with(['owner', 'city', 'compound', 'developer', 'type', 'media', 'reviews.user', 'amenities'])
            ->where('status', 'approved')
            ->where('is_visible', true)
            ->findOrFail($id);
    }

    public function getLatestUnits($limit = 6)
    {
        return Unit::with(['owner', 'city', 'compound', 'developer', 'type', 'media', 'amenities'])
            ->where('status', 'approved')
            ->where('is_visible', true)
            ->latest()
            ->take($limit)
            ->get();
    }
    public function getRelatedUnits($unitId, $limit = 10)
    {
        $unit = Unit::findOrFail($unitId);

        return Unit::with(['owner', 'city', 'compound', 'developer', 'type', 'media', 'amenities'])
            ->where('status', 'approved')
            ->where('is_visible', true)
            ->where('id', '!=', $unitId)
            ->where(function ($query) use ($unit) {
                $query->where('unit_type_id', $unit->unit_type_id)
                    ->orWhere('city_id', $unit->city_id);
            })
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getNearbyUnits($user, $perPage = 10)
    {
        $query = Unit::with(['owner', 'city', 'compound', 'developer', 'type', 'media', 'amenities'])
            ->where('status', 'approved')
            ->where('is_visible', true);


        if (!$user || !$user->city_id) {
            return $query->where('id', 0)->paginate($perPage); // Return empty paginator
        }

        return $query->where('city_id', $user->city_id)
            ->latest()
            ->paginate($perPage);
    }
}
