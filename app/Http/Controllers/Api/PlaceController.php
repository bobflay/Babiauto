<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Search the place catalogue (the "Où allez-vous ?" destination list).
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['sometimes', 'nullable', 'string', 'max:120'],
            'limit' => ['sometimes', 'integer', 'between:1,50'],
        ]);

        $term = trim($validated['q'] ?? '');
        $limit = $validated['limit'] ?? 20;

        $query = Place::query();

        if ($term !== '') {
            $query->search($term);
        }

        $places = $query
            ->orderByDesc('popularity')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return PlaceResource::collection($places);
    }
}
