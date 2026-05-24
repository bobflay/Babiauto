<?php

namespace App\Http\Requests\PaymentMethod;

use App\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(PaymentType::class)],
            'provider' => ['sometimes', 'nullable', 'string', 'max:64'],
            'last4' => ['sometimes', 'nullable', 'digits:4'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
