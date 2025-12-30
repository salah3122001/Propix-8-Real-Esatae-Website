<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitMedia extends Model
{
    //
    use HasFactory;

    protected $fillable = ['unit_id', 'type', 'url', 'order'];

    public function unit() {
        return $this->belongsTo(Unit::class);
    }
}
