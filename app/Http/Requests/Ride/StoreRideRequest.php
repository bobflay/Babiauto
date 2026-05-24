<?php

namespace App\Http\Requests\Ride;

use App\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreRideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_class' => ['required', 'string', Rule::exists('vehicle_classes', 'slug')->where('is_active', true)],
            'payment_type' => ['required', new Enum(PaymentType::class)],

            'pickup' => ['required', 'array'],
            'pickup.name' => ['required', 'string', 'max:255'],
            'pickup.lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup.lng' => ['required', 'numeric', 'between:-180,180'],

            'dropoff' => ['required', 'array'],
            'dropoff.name' => ['required', 'string', 'max:255'],
            'dropoff.lat' => ['required', 'numeric', 'between:-90,90'],
            'dropoff.lng' => ['required', 'numeric', 'between:-180,180'],

            'is_airport' => ['sometimes', 'boolean'],
        ];
    }
}
