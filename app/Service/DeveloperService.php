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
        return Developer::where('status', 1)->find($id);
    }
}
