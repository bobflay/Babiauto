<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RideRating extends Model
{
    protected $fillable = [
        'ride_id',
        'user_id',
        'driver_id',
        'stars',
        'tip',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'stars' => 'integer',
            'tip' => 'integer',
        ];
    }

    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
