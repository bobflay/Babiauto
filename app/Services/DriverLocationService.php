<?php

namespace App\Services;

use App\Models\Driver;
use Illuminate\Support\Facades\Redis;
use Throwable;

/**
 * Tracks live driver positions in Redis.
 *
 * - A geospatial index ("drivers:available") powers nearest-driver matching.
 * - A per-ride hash ("ride:{id}:driver_loc") holds the position streamed to the
 *   rider while a trip is active, with a TTL so stale rides expire on their own.
 *
 * All Redis access is wrapped so the core ride flow still works (falling back to
 * the driver's last known DB position) if Redis is briefly unavailable.
 */
class DriverLocationService
{
    private const GEO_KEY = 'drivers:available';

    private function rideKey(int $rideId): string
    {
        return "ride:{$rideId}:driver_loc";
    }

    /**
     * Add/refresh a driver in the availability geo-index.
     */
    public function markAvailable(Driver $driver): void
    {
        if ($driver->current_lat === null || $driver->current_lng === null) {
            return;
        }

        $this->safely(function () use ($driver) {
            Redis::geoadd(self::GEO_KEY, $driver->current_lng, $driver->current_lat, (string) $driver->id);
        });
    }

    /**
     * Remove a driver from the availability geo-index (e.g. when assigned).
     */
    public function markUnavailable(Driver $driver): void
    {
        $this->safely(function () use ($driver) {
            Redis::zrem(self::GEO_KEY, (string) $driver->id);
        });
    }

    /**
     * Find ids of available drivers within $radiusKm of the pickup, nearest first.
     *
     * @return array<int, int>|null Null when the index is unavailable/empty.
     */
    public function nearbyDriverIds(float $lat, float $lng, float $radiusKm, int $limit = 10): ?array
    {
        return $this->safely(function () use ($lat, $lng, $radiusKm, $limit) {
            $results = Redis::geosearch(
                self::GEO_KEY,
                [$lng, $lat],
                $radiusKm,
                'km',
                ['ASC', 'COUNT' => $limit]
            );

            if (empty($results)) {
                return null;
            }

            return array_map('intval', $results);
        });
    }

    /**
     * Stream a position update for the driver assigned to a ride.
     */
    public function pushRideLocation(int $rideId, float $lat, float $lng, ?float $heading = null): void
    {
        $payload = [
            'lat' => $lat,
            'lng' => $lng,
            'heading' => $heading,
            'updated_at' => now()->toIso8601String(),
        ];

        $this->safely(function () use ($rideId, $payload) {
            $key = $this->rideKey($rideId);
            Redis::hmset($key, $payload);
            Redis::expire($key, (int) config('babiauto.driver_location_ttl'));
        });
    }

    /**
     * Read the latest streamed driver position for a ride.
     *
     * @return array<string, mixed>|null
     */
    public function rideLocation(int $rideId): ?array
    {
        $data = $this->safely(function () use ($rideId) {
            $value = Redis::hgetall($this->rideKey($rideId));

            return empty($value) ? null : $value;
        });

        if (! $data) {
            return null;
        }

        return [
            'lat' => isset($data['lat']) ? (float) $data['lat'] : null,
            'lng' => isset($data['lng']) ? (float) $data['lng'] : null,
            'heading' => isset($data['heading']) && $data['heading'] !== '' ? (float) $data['heading'] : null,
            'updated_at' => $data['updated_at'] ?? null,
        ];
    }

    public function clearRideLocation(int $rideId): void
    {
        $this->safely(fn () => Redis::del($this->rideKey($rideId)));
    }

    /**
     * Run a Redis closure, swallowing connection errors so ride flow degrades gracefully.
     */
    private function safely(callable $fn): mixed
    {
        try {
            return $fn();
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
