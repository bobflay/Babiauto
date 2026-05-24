<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\VehicleClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Driver> */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        $name = $this->faker->firstName().' '.strtoupper($this->faker->randomLetter()).'.';

        return [
            'vehicle_class_id' => VehicleClass::factory(),
            'name' => $name,
            'avatar_initial' => mb_substr($name, 0, 1),
            'phone' => '+225 07 00 00 00 00',
            'rating' => 4.90,
            'trips_count' => 100,
            'car_make' => 'Toyota Corolla',
            'car_color_fr' => 'noir',
            'car_color_en' => 'black',
            'plate' => strtoupper($this->faker->bothify('#### ?? 01')),
            'is_available' => true,
            'current_lat' => 5.3560,
            'current_lng' => -3.9870,
        ];
    }

    public function forClass(VehicleClass $class): static
    {
        return $this->state(fn () => ['vehicle_class_id' => $class->id]);
    }
}
