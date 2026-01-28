<?php

namespace App\Service;

use App\Models\Developer;

class DeveloperService
{
    public function getAllDevelopers()
    {
        return Developer::where('status', 1)->get();
    }

    public function getDeveloperById($id)
    {
        return Developer::where('status', 1)->with(['units', 'units.media', 'units.type', 'units.city', 'units.compound'])->find($id);
    }

    public function getDeveloperUnits($id, $perPage = 10)
    {
        $developer = Developer::where('status', 1)->find($id);
        if (!$developer) return null;

        return $developer->units()
            ->with(['media', 'type', 'city', 'compound'])
            ->latest()
            ->paginate($perPage);
    }
}
