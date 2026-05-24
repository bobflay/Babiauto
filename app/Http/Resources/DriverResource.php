<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Driver */
class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar_initial' => $this->avatar_initial,
            'phone' => $this->phone,
            'rating' => (float) $this->rating,
            'trips_count' => $this->trips_count,
            'vehicle' => [
                'make' => $this->car_make,
                'color' => [
                    'fr' => $this->car_color_fr,
                    'en' => $this->car_color_en,
                ],
                'plate' => $this->plate,
                'class' => VehicleClassResource::make($this->whenLoaded('vehicleClass')),
            ],
        ];
    }
}
