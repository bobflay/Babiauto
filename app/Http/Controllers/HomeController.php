<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Place;
use App\Models\VehicleClass;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $vehicleClasses = VehicleClass::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $stats = [
            'drivers' => Driver::count(),
            'vehicle_classes' => $vehicleClasses->count(),
            'neighborhoods' => Place::query()->distinct()->count('neighborhood'),
        ];

        return view('home', [
            'vehicleClasses' => $vehicleClasses,
            'stats' => $stats,
        ]);
    }
}
