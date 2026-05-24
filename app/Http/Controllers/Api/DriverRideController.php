<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ride\DriverLocationRequest;
use App\Http\Resources\RideResource;
use App\Models\Ride;
use App\Services\DriverLocationService;
use App\Services\RideService;
use Illuminate\Http\JsonResponse;

/**
 * Driver / dispatch side of the ride lifecycle.
 *
 * In production these endpoints would sit behind a dedicated driver guard; the
 * Babiauto design ships a rider app only, so for this backend they are exposed
 * under the same Sanctum auth to drive a ride through its states end-to-end.
 */
class DriverRideController extends Controller
{
    public function __construct(
        private RideService $rides,
        private DriverLocationService $locations,
    ) {}

    public function arriving(Ride $ride): JsonResponse
    {
        return $this->respond($this->rides->markArriving($ride));
    }

    public function arrived(Ride $ride): JsonResponse
    {
        return $this->respond($this->rides->markArrived($ride));
    }

    public function start(Ride $ride): JsonResponse
    {
        return $this->respond($this->rides->startTrip($ride));
    }

    public function complete(Ride $ride): JsonResponse
    {
        return $this->respond($this->rides->completeTrip($ride));
    }

    public function updateLocation(DriverLocationRequest $request, Ride $ride): JsonResponse
    {
        abort_unless($ride->status->isActive(), 422, 'Ride is not active.');

        $data = $request->validated();
        $this->locations->pushRideLocation($ride->id, $data['lat'], $data['lng'], $data['heading'] ?? null);

        if ($ride->driver) {
            $ride->driver->update(['current_lat' => $data['lat'], 'current_lng' => $data['lng']]);
        }

        return response()->json([
            'ride_id' => $ride->id,
            'location' => $this->locations->rideLocation($ride->id),
        ]);
    }

    private function respond(Ride $ride): JsonResponse
    {
        $ride->load(['driver.vehicleClass', 'vehicleClass']);

        if ($ride->status->isActive() && $ride->driver_id) {
            $ride->setAttribute('live_location', $this->locations->rideLocation($ride->id));
        }

        return RideResource::make($ride)->response();
    }
}
