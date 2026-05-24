<?php

namespace App\Http\Resources;

use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ride */
class RideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'payment_type' => $this->payment_type->value,
            'pickup' => [
                'name' => $this->pickup_name,
                'lat' => $this->pickup_lat,
                'lng' => $this->pickup_lng,
            ],
            'dropoff' => [
                'name' => $this->dropoff_name,
                'lat' => $this->dropoff_lat,
                'lng' => $this->dropoff_lng,
            ],
            'distance_km' => $this->distance_km,
            'duration_minutes' => $this->duration_minutes,
            'fare' => [
                'base_fare' => $this->base_fare,
                'airport_fee' => $this->airport_fee,
                'tip' => $this->tip,
                'total' => $this->total_fare,
                'currency' => 'XOF',
            ],
            'vehicle_class' => VehicleClassResource::make($this->whenLoaded('vehicleClass')),
            'driver' => DriverResource::make($this->whenLoaded('driver')),
            'rating' => $this->whenLoaded('rating', fn () => $this->rating ? [
                'stars' => $this->rating->stars,
                'tip' => $this->rating->tip,
                'comment' => $this->rating->comment,
            ] : null),
            'tracking' => $this->when(
                $this->resource->relationLoaded('driver') && $this->getAttribute('live_location') !== null,
                fn () => $this->getAttribute('live_location'),
            ),
            'timestamps' => [
                'requested_at' => $this->requested_at,
                'accepted_at' => $this->accepted_at,
                'arrived_at' => $this->arrived_at,
                'started_at' => $this->started_at,
                'completed_at' => $this->completed_at,
                'cancelled_at' => $this->cancelled_at,
            ],
            'cancel_reason' => $this->cancel_reason,
            'created_at' => $this->created_at,
        ];
    }
}
