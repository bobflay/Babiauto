<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_class_id',
        'name',
        'avatar_initial',
        'phone',
        'rating',
        'trips_count',
        'car_make',
        'car_color_fr',
        'car_color_en',
        'plate',
        'is_available',
        'current_lat',
        'current_lng',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'trips_count' => 'integer',
            'is_available' => 'boolean',
            'current_lat' => 'float',
            'current_lng' => 'float',
        ];
    }

    public function vehicleClass(): BelongsTo
    {
        return $this->belongsTo(VehicleClass::class);
    }

    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
