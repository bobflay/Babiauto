<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description_fr',
        'description_en',
        'seats',
        'default_eta_minutes',
        'booking_fee',
        'per_km',
        'per_minute',
        'min_fare',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
            'default_eta_minutes' => 'integer',
            'booking_fee' => 'integer',
            'per_km' => 'integer',
            'per_minute' => 'integer',
            'min_fare' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * Compute the fare for a trip of the given distance/duration, before surcharges.
     */
    public function fareFor(float $distanceKm, int $durationMinutes): int
    {
        $raw = $this->booking_fee
            + ($this->per_km * $distanceKm)
            + ($this->per_minute * $durationMinutes);

        $fare = max($this->min_fare, (int) round($raw / 50) * 50);

        return (int) $fare;
    }
}
