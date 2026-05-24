<?php

namespace App\Http\Resources;

use App\Models\VehicleClass;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin VehicleClass */
class VehicleClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => [
                'fr' => $this->description_fr,
                'en' => $this->description_en,
            ],
            'seats' => $this->seats,
            'eta_minutes' => $this->default_eta_minutes,
            'pricing' => [
                'booking_fee' => $this->booking_fee,
                'per_km' => $this->per_km,
                'per_minute' => $this->per_minute,
                'min_fare' => $this->min_fare,
                'currency' => 'XOF',
            ],
            'sort_order' => $this->sort_order,
        ];
    }
}
