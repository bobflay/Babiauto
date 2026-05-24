<?php

namespace App\Http\Resources;

use App\Models\SavedPlace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin SavedPlace */
class SavedPlaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'icon' => $this->icon,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
