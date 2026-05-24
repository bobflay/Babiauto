<?php

namespace App\Services;

use App\Models\Driver;
use App\Support\Geo;
use Illuminate\Support\Collection;

/**
 * Produces simulated live positions for drivers drifting around a rider.
 *
 * Each driver is given a stable orbit (radius, start angle, angular speed and
 * direction) derived from its id, then placed on that orbit at the current
 * time. Because the position is a pure function of (driver id, time), repeated
 * polls a few seconds apart return smoothly moving cars with no background
 * worker — ideal for the home-screen "cars around me" map in the demo.
 */
class DriverSimulator
{
    private const KM_PER_DEG_LAT = 110.574;

    private const KM_PER_DEG_LNG = 111.320;

    /**
     * @param  Collection<int, Driver>  $drivers
     * @return Collection<int, array<string, mixed>>
     */
    public function positionsAround(Collection $drivers, float $lat, float $lng, ?int $atSecond = null): Collection
    {
        $t = $atSecond ?? now()->getTimestamp();
        $maxRadiusKm = max(0.2, (float) config('babiauto.simulate_radius_km'));

        return $drivers->map(function (Driver $driver) use ($lat, $lng, $maxRadiusKm, $t) {
            $orbitRadiusKm = $maxRadiusKm * (0.35 + 0.60 * $this->frac($driver->id * 0.6180339887));
            $angularSpeed = 0.04 + 0.05 * $this->frac($driver->id * 0.7548776662); // rad/s
            $angle0 = 2 * M_PI * $this->frac($driver->id * 0.3819660112);
            $direction = $driver->id % 2 === 0 ? 1 : -1;

            $angle = $angle0 + $direction * $angularSpeed * $t;

            $eastKm = $orbitRadiusKm * cos($angle);
            $northKm = $orbitRadiusKm * sin($angle);

            $pLat = $lat + ($northKm / self::KM_PER_DEG_LAT);
            $pLng = $lng + ($eastKm / (self::KM_PER_DEG_LNG * cos(deg2rad($lat))));

            // Heading = compass bearing of the orbital velocity (tangent).
            $vEast = -sin($angle) * $direction;
            $vNorth = cos($angle) * $direction;
            $heading = fmod(rad2deg(atan2($vEast, $vNorth)) + 360, 360);

            $distanceKm = Geo::distanceKm($lat, $lng, $pLat, $pLng);

            return $this->shape($driver, $pLat, $pLng, round($heading, 1), $distanceKm, true);
        })->values();
    }

    /**
     * Build the response shape for a driver at a known position.
     *
     * @return array<string, mixed>
     */
    public function shape(Driver $driver, float $lat, float $lng, ?float $heading, float $distanceKm, bool $simulated): array
    {
        $speed = max(1, (float) config('babiauto.avg_speed_kmh'));

        return [
            'id' => $driver->id,
            'vehicle_class' => $driver->vehicleClass?->slug,
            'lat' => round($lat, 6),
            'lng' => round($lng, 6),
            'heading' => $heading,
            'distance_km' => round($distanceKm, 2),
            'eta_minutes' => max(1, (int) ceil(($distanceKm / $speed) * 60)),
            'simulated' => $simulated,
        ];
    }

    private function frac(float $x): float
    {
        return $x - floor($x);
    }
}
