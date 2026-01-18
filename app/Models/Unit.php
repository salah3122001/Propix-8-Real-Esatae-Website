<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    //
    use HasFactory;

    protected static function booted()
    {
        static::updated(function (Unit $unit) {
            if ($unit->wasChanged('status')) {
                if ($unit->status === 'approved') {
                    // Notify buyers in the same city
                    $buyers = \App\Models\User::where('role', 'buyer')
                        ->where('city_id', $unit->city_id)
                        ->whereNotNull('email_verified_at')
                        ->get();

                    foreach ($buyers as $buyer) {
                        $buyer->notify(new \App\Notifications\NewUnitAddedNotification($unit));
                    }
                }
            }
        });
    }

    protected $fillable = [
        'title_ar',
        'title_en',
        'type',
        'description_ar',
        'description_en',
        'address',
        'price',
        'price_per_m2',
        'offer_type',
        'area',
        'rooms',
        'bathrooms',
        'garages',
        'build_year',
        'land_area',
        'internal_area',
        'status',
        'city_id',
        'unit_type_id',
        'compound_id',
        'developer_id',
        'latitude',
        'longitude',
        'sold_at',
        'rented_at',
        'development_status',
        'is_visible',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function type()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }

    public function media()
    {
        return $this->hasMany(UnitMedia::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->title_ar : $this->title_en;
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'sold_at' => 'datetime',
            'rented_at' => 'datetime',
            'is_visible' => 'boolean',
        ];
    }
}
