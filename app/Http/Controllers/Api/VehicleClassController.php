<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleClassResource;
use App\Models\VehicleClass;
use Illuminate\Support\Facades\Cache;

class VehicleClassController extends Controller
{
    public function index()
    {
        $classes = Cache::remember('vehicle_classes:active', now()->addHour(), function () {
            return VehicleClass::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        return VehicleClassResource::collection($classes);
    }
}
