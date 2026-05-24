<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ride\CancelRideRequest;
use App\Http\Requests\Ride\EstimateRideRequest;
use App\Http\Requests\Ride\RateRideRequest;
use App\Http\Requests\Ride\StoreRideRequest;
use App\Http\Resources\RideResource;
use App\Models\Place;
use App\Models\Ride;
use App\Models\VehicleClass;
use App\Services\DriverLocationService;
use App\Services\FareEstimator;
use App\Services\RideService;
use App\Support\Geo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function __construct(
        private RideService $rides,
        private FareEstimator $fares,
        private DriverLocationService $locations,
    ) {}

    /**
     * Fare quotes for every vehicle class for a pickup/dropoff (Vehicle screen).
     */
    public function estimate(EstimateRideRequest $request): JsonResponse
    {
        $data = $request->validated();

        $isAirport = $data['is_airport']
            ?? $this->looksLikeAirportTrip($data['pickup'], $data['dropoff']);

        $quotes = $this->fares->quoteAll(
            $data['pickup']['lat'],
            $data['pickup']['lng'],
            $data['dropoff']['lat'],
            $data['dropoff']['lng'],
            $isAirport,
        )->map(fn (array $quote) => [
            'vehicle_class' => [
                'id' => $quote['vehicle_class']->id,
                'slug' => $quote['vehicle_class']->slug,
                'name' => $quote['vehicle_class']->name,
                'seats' => $quote['vehicle_class']->seats,
            ],
            'distance_km' => $quote['distance_km'],
            'duration_minutes' => $quote['duration_minutes'],
            'base_fare' => $quote['base_fare'],
            'airport_fee' => $quote['airport_fee'],
            'total_fare' => $quote['total_fare'],
            'driver_eta_minutes' => $quote['driver_eta_minutes'],
            'currency' => $quote['currency'],
        ]);

        return response()->json(['data' => $quotes]);
    }

    public function index(Request $request)
    {
        $rides = $request->user()
            ->rides()
            ->with(['driver.vehicleClass', 'vehicleClass', 'rating'])
            ->latest()
            ->paginate(15);

        return RideResource::collection($rides);
    }

    public function store(StoreRideRequest $request): JsonResponse
    {
        $data = $request->validated();
        $class = VehicleClass::where('slug', $data['vehicle_class'])->firstOrFail();

        $isAirport = $data['is_airport']
            ?? $this->looksLikeAirportTrip($data['pickup'], $data['dropoff']);

        $ride = $this->rides->request(
            $request->user(),
            $class,
            PaymentType::from($data['payment_type']),
            $data['pickup'],
            $data['dropoff'],
            $isAirport,
        );

        return $this->respondWithRide($ride, 201);
    }

    public function show(Request $request, Ride $ride): JsonResponse
    {
        $this->authorizeRide($request, $ride);

        return $this->respondWithRide($ride);
    }

    public function cancel(CancelRideRequest $request, Ride $ride): JsonResponse
    {
        $this->authorizeRide($request, $ride);

        $ride = $this->rides->cancel($ride, $request->validated()['reason'] ?? null);

        return $this->respondWithRide($ride);
    }

    public function rate(RateRideRequest $request, Ride $ride): JsonResponse
    {
        $this->authorizeRide($request, $ride);

        $data = $request->validated();
        $this->rides->rate($ride, $data['stars'], $data['tip'] ?? 0, $data['comment'] ?? null);

        return $this->respondWithRide($ride->refresh());
    }

    /**
     * Live driver position for an active ride (Finding / Arriving / On-trip screens).
     */
    public function tracking(Request $request, Ride $ride): JsonResponse
    {
        $this->authorizeRide($request, $ride);

        return response()->json([
            'ride_id' => $ride->id,
            'status' => $ride->status->value,
            'location' => $this->locations->rideLocation($ride->id),
        ]);
    }

    private function respondWithRide(Ride $ride, int $status = 200): JsonResponse
    {
        $ride->load(['driver.vehicleClass', 'vehicleClass', 'rating']);

        if ($ride->status->isActive() && $ride->driver_id) {
            $ride->setAttribute('live_location', $this->locations->rideLocation($ride->id));
        }

        return RideResource::make($ride)->response()->setStatusCode($status);
    }

    private function authorizeRide(Request $request, Ride $ride): void
    {
        abort_unless($ride->user_id === $request->user()->id, 403);
    }

    /**
     * @param  array{lat:float,lng:float}  $pickup
     * @param  array{lat:float,lng:float}  $dropoff
     */
    private function looksLikeAirportTrip(array $pickup, array $dropoff): bool
    {
        return Place::query()
            ->where('is_airport', true)
            ->get()
            ->contains(function (Place $airport) use ($pickup, $dropoff) {
                return Geo::distanceKm($airport->lat, $airport->lng, $pickup['lat'], $pickup['lng']) <= 1.0
                    || Geo::distanceKm($airport->lat, $airport->lng, $dropoff['lat'], $dropoff['lng']) <= 1.0;
            });
    }
}
