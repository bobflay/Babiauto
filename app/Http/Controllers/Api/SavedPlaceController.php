<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavedPlace\StoreSavedPlaceRequest;
use App\Http\Resources\SavedPlaceResource;
use App\Models\SavedPlace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedPlaceController extends Controller
{
    public function index(Request $request)
    {
        return SavedPlaceResource::collection(
            $request->user()->savedPlaces()->latest()->get()
        );
    }

    public function store(StoreSavedPlaceRequest $request): JsonResponse
    {
        $place = $request->user()->savedPlaces()->create($request->validated());

        return SavedPlaceResource::make($place)
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request, SavedPlace $savedPlace): JsonResponse
    {
        abort_unless($savedPlace->user_id === $request->user()->id, 403);

        $savedPlace->delete();

        return response()->json(null, 204);
    }
}
