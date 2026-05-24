<?php

namespace Database\Factories;

use App\Models\VehicleClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<VehicleClass> */
class VehicleClassFactory extends Factory
{
    protected $model = VehicleClass::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'slug' => Str::slug($name),
            'name' => 'Babi '.ucfirst($name),
            'description_fr' => 'Confortable',
            'description_en' => 'Comfortable',
            'seats' => 4,
            'default_eta_minutes' => 4,
            'booking_fee' => 500,
            'per_km' => 112,
            'per_minute' => 10,
            'min_fare' => 1500,
            'sort_order' => 1,
            'is_active' => true,
        ];
    }
}
