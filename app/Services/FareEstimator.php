<?php

namespace App\Services;

use App\Models\VehicleClass;
use App\Support\Geo;
use Illuminate\Support\Collection;

class FareEstimator
{
    /**
     * Build a fare quote for every active vehicle class for the given trip.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function quoteAll(
        float $pickupLat,
        float $pickupLng,
        float $dropoffLat,
        float $dropoffLng,
        bool $isAirportTrip = false
    ): Collection {
        $distanceKm = round(Geo::distanceKm($pickupLat, $pickupLng, $dropoffLat, $dropoffLng), 2);
        $durationMinutes = $this->estimateDuration($distanceKm);
        $airportFee = $isAirportTrip ? (int) config('babiauto.airport_fee') : 0;

        return VehicleClass::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (VehicleClass $class) => $this->quoteFor($class, $distanceKm, $durationMinutes, $airportFee));
    }

    /**
     * Build a single fare quote for one vehicle class.
     *
     * @return array<string, mixed>
     */
    public function quoteFor(
        VehicleClass $class,
        float $distanceKm,
        int $durationMinutes,
        int $airportFee
    ): array {
        $baseFare = $class->fareFor($distanceKm, $durationMinutes);

        return [
            'vehicle_class' => $class,
            'distance_km' => $distanceKm,
            'duration_minutes' => $durationMinutes,
            'base_fare' => $baseFare,
            'airport_fee' => $airportFee,
            'total_fare' => $baseFare + $airportFee,
            'driver_eta_minutes' => $class->default_eta_minutes,
            'currency' => 'XOF',
        ];
    }

    public function estimateDuration(float $distanceKm): int
    {
        $speed = max(1, (float) config('babiauto.avg_speed_kmh'));

        return max(1, (int) ceil(($distanceKm / $speed) * 60));
    }
}
