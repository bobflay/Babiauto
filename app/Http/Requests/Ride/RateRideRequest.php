<?php

namespace App\Http\Requests\Ride;

use Illuminate\Foundation\Http\FormRequest;

class RateRideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stars' => ['required', 'integer', 'between:1,5'],
            'tip' => ['sometimes', 'integer', 'min:0', 'max:'.config('babiauto.max_tip')],
            'comment' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
