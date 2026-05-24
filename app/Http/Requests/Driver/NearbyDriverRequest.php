<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NearbyDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'vehicle_class' => ['sometimes', 'nullable', 'string', Rule::exists('vehicle_classes', 'slug')->where('is_active', true)],
            'limit' => ['sometimes', 'integer', 'between:1,50'],
        ];
    }
}
