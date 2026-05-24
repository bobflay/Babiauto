<?php

namespace App\Services;

use App\Enums\PaymentType;
use App\Enums\RideStatus;
use App\Exceptions\InvalidRideTransitionException;
use App\Models\Driver;
use App\Models\Ride;
use App\Models\RideRating;
use App\Models\User;
use App\Models\VehicleClass;
use App\Support\Geo;
use Illuminate\Support\Facades\DB;

class RideService
{
    public function __construct(
        private FareEstimator $fares,
        private DriverMatcher $matcher,
        private DriverLocationService $locations,
    ) {}

    /**
     * Create a ride request and attempt to match a driver immediately.
     *
     * @param  array{name:string,lat:float,lng:float}  $pickup
     * @param  array{name:string,lat:float,lng:float}  $dropoff
     */
    public function request(
        User $user,
        VehicleClass $class,
        PaymentType $paymentType,
        array $pickup,
        array $dropoff,
        bool $isAirportTrip = false,
    ): Ride {
        $distanceKm = round(Geo::distanceKm($pickup['lat'], $pickup['lng'], $dropoff['lat'], $dropoff['lng']), 2);
        $quote = $this->fares->quoteFor(
            $class,
            $distanceKm,
            $this->fares->estimateDuration($distanceKm),
            $isAirportTrip ? (int) config('babiauto.airport_fee') : 0,
        );

        $ride = DB::transaction(function () use ($user, $class, $paymentType, $pickup, $dropoff, $quote) {
            $ride = new Ride([
                'vehicle_class_id' => $class->id,
                'status' => RideStatus::Searching,
                'payment_type' => $paymentType,
                'pickup_name' => $pickup['name'],
                'pickup_lat' => $pickup['lat'],
                'pickup_lng' => $pickup['lng'],
                'dropoff_name' => $dropoff['name'],
                'dropoff_lat' => $dropoff['lat'],
                'dropoff_lng' => $dropoff['lng'],
                'distance_km' => $quote['distance_km'],
                'duration_minutes' => $quote['duration_minutes'],
                'base_fare' => $quote['base_fare'],
                'airport_fee' => $quote['airport_fee'],
                'tip' => 0,
                'requested_at' => now(),
            ]);
            $ride->user()->associate($user);
            $ride->recomputeTotal();
            $ride->save();

            return $ride;
        });

        $this->assignDriver($ride);

        return $ride->refresh()->load(['driver.vehicleClass', 'vehicleClass']);
    }

    /**
     * Try to assign the nearest available driver to a searching ride.
     */
    public function assignDriver(Ride $ride): ?Driver
    {
        if ($ride->status !== RideStatus::Searching) {
            return null;
        }

        $driver = $this->matcher->findFor($ride->vehicleClass, $ride->pickup_lat, $ride->pickup_lng);

        if (! $driver) {
            return null;
        }

        DB::transaction(function () use ($ride, $driver) {
            $driver->update(['is_available' => false]);

            $ride->driver()->associate($driver);
            $ride->status = RideStatus::Accepted;
            $ride->accepted_at = now();
            $ride->save();
        });

        $this->locations->markUnavailable($driver);

        if ($driver->current_lat !== null && $driver->current_lng !== null) {
            $this->locations->pushRideLocation($ride->id, $driver->current_lat, $driver->current_lng);
        }

        return $driver;
    }

    public function markArriving(Ride $ride): Ride
    {
        return $this->transition($ride, RideStatus::Arriving, ['accepted_at' => $ride->accepted_at ?? now()]);
    }

    public function markArrived(Ride $ride): Ride
    {
        return $this->transition($ride, RideStatus::Arrived, ['arrived_at' => now()]);
    }

    public function startTrip(Ride $ride): Ride
    {
        return $this->transition($ride, RideStatus::InProgress, ['started_at' => now()]);
    }

    public function completeTrip(Ride $ride): Ride
    {
        $ride = $this->transition($ride, RideStatus::Completed, ['completed_at' => now()]);

        if ($ride->driver) {
            $driver = $ride->driver;
            $driver->increment('trips_count');
            $driver->update(['is_available' => true]);
            $this->locations->markAvailable($driver);
        }

        $this->locations->clearRideLocation($ride->id);

        return $ride;
    }

    public function cancel(Ride $ride, ?string $reason = null): Ride
    {
        if ($ride->status->isTerminal()) {
            throw new InvalidRideTransitionException(
                "Ride #{$ride->id} is already {$ride->status->value} and cannot be cancelled."
            );
        }

        $driver = $ride->driver;

        DB::transaction(function () use ($ride, $reason, $driver) {
            $ride->status = RideStatus::Cancelled;
            $ride->cancelled_at = now();
            $ride->cancel_reason = $reason;
            $ride->save();

            if ($driver) {
                $driver->update(['is_available' => true]);
            }
        });

        if ($driver) {
            $this->locations->markAvailable($driver);
        }
        $this->locations->clearRideLocation($ride->id);

        return $ride;
    }

    public function rate(Ride $ride, int $stars, int $tip = 0, ?string $comment = null): RideRating
    {
        if ($ride->status !== RideStatus::Completed) {
            throw new InvalidRideTransitionException('Only completed rides can be rated.');
        }

        if (! $ride->driver_id) {
            throw new InvalidRideTransitionException('Ride has no driver to rate.');
        }

        return DB::transaction(function () use ($ride, $stars, $tip, $comment) {
            if ($tip > 0) {
                $ride->tip = $tip;
                $ride->recomputeTotal();
                $ride->save();
            }

            $rating = RideRating::updateOrCreate(
                ['ride_id' => $ride->id],
                [
                    'user_id' => $ride->user_id,
                    'driver_id' => $ride->driver_id,
                    'stars' => $stars,
                    'tip' => $tip,
                    'comment' => $comment,
                ],
            );

            $this->refreshDriverRating($ride->driver);

            return $rating;
        });
    }

    /**
     * Move the ride to a new status, enforcing the allowed transition graph.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function transition(Ride $ride, RideStatus $to, array $attributes = []): Ride
    {
        if (! $ride->status->canTransitionTo($to)) {
            throw new InvalidRideTransitionException(
                "Cannot move ride #{$ride->id} from {$ride->status->value} to {$to->value}."
            );
        }

        $ride->fill($attributes);
        $ride->status = $to;
        $ride->save();

        return $ride;
    }

    private function refreshDriverRating(Driver $driver): void
    {
        $average = $driver->rides()
            ->whereHas('rating')
            ->with('rating')
            ->get()
            ->pluck('rating.stars')
            ->filter()
            ->avg();

        if ($average !== null) {
            $driver->update(['rating' => round($average, 2)]);
        }
    }
}
