<?php

return [
    /*
    | Surcharge applied to trips that start or end at an airport (F CFA).
    */
    'airport_fee' => env('BABIAUTO_AIRPORT_FEE', 400),

    /*
    | Average city driving speed (km/h) used to estimate trip duration.
    */
    'avg_speed_kmh' => env('BABIAUTO_AVG_SPEED_KMH', 32),

    /*
    | Max radius (km) within which a driver may be matched to a pickup.
    */
    'match_radius_km' => env('BABIAUTO_MATCH_RADIUS_KM', 8),

    /*
    | TTL (seconds) for a driver's live location stored in Redis.
    */
    'driver_location_ttl' => env('BABIAUTO_DRIVER_LOCATION_TTL', 900),

    /*
    | Allowed tip presets (F CFA) and the ride rating bounds.
    */
    'tip_presets' => [0, 200, 500, 1000],
    'max_tip' => env('BABIAUTO_MAX_TIP', 50000),
];
