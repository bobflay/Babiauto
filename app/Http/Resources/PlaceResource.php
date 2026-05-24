<?php

namespace App\Http\Resources;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Place */
class PlaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'icon' => $this->icon,
            'neighborhood' => $this->neighborhood,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'is_airport' => $this->is_airport,
        ];
    }
}
