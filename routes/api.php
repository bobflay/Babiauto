<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverRideController;
use App\Http\Controllers\Api\NearbyDriverController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RideController;
use App\Http\Controllers\Api\SavedPlaceController;
use App\Http\Controllers\Api\VehicleClassController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::get('vehicle-classes', [VehicleClassController::class, 'index']);
    Route::get('places', [PlaceController::class, 'index']);
    Route::get('drivers/nearby', [NearbyDriverController::class, 'index']);
    Route::post('rides/estimate', [RideController::class, 'estimate']);

    // Authenticated (rider)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('profile', [ProfileController::class, 'show']);
        Route::patch('profile', [ProfileController::class, 'update']);

        Route::get('saved-places', [SavedPlaceController::class, 'index']);
        Route::post('saved-places', [SavedPlaceController::class, 'store']);
        Route::delete('saved-places/{savedPlace}', [SavedPlaceController::class, 'destroy']);

        Route::get('payment-methods', [PaymentMethodController::class, 'index']);
        Route::post('payment-methods', [PaymentMethodController::class, 'store']);
        Route::delete('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);

        Route::get('rides', [RideController::class, 'index']);
        Route::post('rides', [RideController::class, 'store']);
        Route::get('rides/{ride}', [RideController::class, 'show']);
        Route::get('rides/{ride}/tracking', [RideController::class, 'tracking']);
        Route::post('rides/{ride}/cancel', [RideController::class, 'cancel']);
        Route::post('rides/{ride}/rate', [RideController::class, 'rate']);

        // Driver / dispatch lifecycle actions
        Route::prefix('rides/{ride}')->group(function () {
            Route::post('arriving', [DriverRideController::class, 'arriving']);
            Route::post('arrived', [DriverRideController::class, 'arrived']);
            Route::post('start', [DriverRideController::class, 'start']);
            Route::post('complete', [DriverRideController::class, 'complete']);
            Route::post('driver-location', [DriverRideController::class, 'updateLocation']);
        });
    });
});
