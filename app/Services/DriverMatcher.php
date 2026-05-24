<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\VehicleClass;
use App\Support\Geo;

class DriverMatcher
{
    public function __construct(private DriverLocationService $locations) {}

    /**
     * Find the closest available driver of the given class to the pickup point.
     *
     * Prefers the Redis geo-index; falls back to a DB haversine scan when Redis
     * has no data (e.g. fresh boot before any location pings).
     */
    public function findFor(VehicleClass $class, float $lat, float $lng): ?Driver
    {
        $radius = (float) config('babiauto.match_radius_km');

        $nearbyIds = $this->locations->nearbyDriverIds($lat, $lng, $radius);

        if ($nearbyIds !== null) {
            $driver = Driver::query()
                ->available()
                ->where('vehicle_class_id', $class->id)
                ->whereIn('id', $nearbyIds)
                ->get()
                ->sortBy(fn (Driver $d) => array_search($d->id, $nearbyIds, true))
                ->first();

            if ($driver) {
                return $driver;
            }
        }

        return $this->findByDatabase($class, $lat, $lng, $radius);
    }

    private function findByDatabase(VehicleClass $class, float $lat, float $lng, float $radius): ?Driver
    {
        return Driver::query()
            ->available()
            ->where('vehicle_class_id', $class->id)
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->get()
            ->filter(fn (Driver $driver) => Geo::distanceKm($lat, $lng, $driver->current_lat, $driver->current_lng) <= $radius)
            ->sortBy(fn (Driver $driver) => Geo::distanceKm($lat, $lng, $driver->current_lat, $driver->current_lng))
            ->first();
    }
}
