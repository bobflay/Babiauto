<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        return PaymentMethodResource::collection(
            $request->user()->paymentMethods()->orderByDesc('is_default')->get()
        );
    }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $method = DB::transaction(function () use ($data, $user) {
            $makeDefault = $data['is_default'] ?? false;

            if ($makeDefault) {
                $user->paymentMethods()->update(['is_default' => false]);
            }

            return $user->paymentMethods()->create([
                'type' => $data['type'],
                'provider' => $data['provider'] ?? null,
                'last4' => $data['last4'] ?? null,
                'is_default' => $makeDefault || $user->paymentMethods()->count() === 0,
            ]);
        });

        return PaymentMethodResource::make($method)
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        abort_unless($paymentMethod->user_id === $request->user()->id, 403);

        $paymentMethod->delete();

        return response()->json(null, 204);
    }
}
