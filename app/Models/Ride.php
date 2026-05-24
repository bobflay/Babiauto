<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Enums\RideStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ride extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_class_id',
        'driver_id',
        'status',
        'payment_type',
        'pickup_name',
        'pickup_lat',
        'pickup_lng',
        'dropoff_name',
        'dropoff_lat',
        'dropoff_lng',
        'distance_km',
        'duration_minutes',
        'base_fare',
        'airport_fee',
        'tip',
        'total_fare',
        'requested_at',
        'accepted_at',
        'arrived_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => RideStatus::class,
            'payment_type' => PaymentType::class,
            'pickup_lat' => 'float',
            'pickup_lng' => 'float',
            'dropoff_lat' => 'float',
            'dropoff_lng' => 'float',
            'distance_km' => 'float',
            'duration_minutes' => 'integer',
            'base_fare' => 'integer',
            'airport_fee' => 'integer',
            'tip' => 'integer',
            'total_fare' => 'integer',
            'requested_at' => 'datetime',
            'accepted_at' => 'datetime',
            'arrived_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicleClass(): BelongsTo
    {
        return $this->belongsTo(VehicleClass::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(RideRating::class);
    }

    public function recomputeTotal(): void
    {
        $this->total_fare = $this->base_fare + $this->airport_fee + $this->tip;
    }
}
