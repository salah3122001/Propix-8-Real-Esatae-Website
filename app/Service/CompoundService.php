<?php

namespace App\Service;

use App\Models\Compound;

class CompoundService
{
    public function getAllCompounds()
    {
        return Compound::with('city')->latest()->get();
    }

    public function getCompoundById(int $id)
    {
        return Compound::with(['city', 'units'])->findOrFail($id);
    }
}
