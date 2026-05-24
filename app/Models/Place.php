<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'name',
        'subtitle',
        'icon',
        'neighborhood',
        'lat',
        'lng',
        'is_airport',
        'popularity',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'is_airport' => 'boolean',
            'popularity' => 'integer',
        ];
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('neighborhood', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%");
        });
    }
}
