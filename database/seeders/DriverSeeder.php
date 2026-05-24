<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\VehicleClass;
use App\Services\DriverLocationService;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(DriverLocationService $locations): void
    {
        $classes = VehicleClass::pluck('id', 'slug');

        // Positions scattered around Cocody / Riviera (near the default pickup).
        $drivers = [
            [
                'slug' => 'confort',
                'name' => 'Kouassi A.',
                'phone' => '+225 07 12 34 56 12',
                'rating' => 4.92,
                'trips_count' => 1847,
                'car_make' => 'Suzuki Alto',
                'car_color_fr' => 'gris',
                'car_color_en' => 'grey',
                'plate' => '2347 BG 01',
                'lat' => 5.3548,
                'lng' => -3.9881,
            ],
            [
                'slug' => 'confort',
                'name' => 'Aya K.',
                'phone' => '+225 07 22 11 09 44',
                'rating' => 4.88,
                'trips_count' => 1203,
                'car_make' => 'Toyota Corolla',
                'car_color_fr' => 'noir',
                'car_color_en' => 'black',
                'plate' => '5521 BD 01',
                'lat' => 5.3582,
                'lng' => -3.9902,
            ],
            [
                'slug' => 'mini',
                'name' => 'Yao B.',
                'phone' => '+225 05 88 77 66 21',
                'rating' => 4.79,
                'trips_count' => 642,
                'car_make' => 'Kia Picanto',
                'car_color_fr' => 'blanc',
                'car_color_en' => 'white',
                'plate' => '1180 AB 01',
                'lat' => 5.3531,
                'lng' => -3.9855,
            ],
            [
                'slug' => 'mini',
                'name' => 'Fatou D.',
                'phone' => '+225 05 33 44 55 78',
                'rating' => 4.84,
                'trips_count' => 988,
                'car_make' => 'Hyundai i10',
                'car_color_fr' => 'rouge',
                'car_color_en' => 'red',
                'plate' => '7762 CC 01',
                'lat' => 5.3605,
                'lng' => -3.9890,
            ],
            [
                'slug' => 'xl',
                'name' => 'Ibrahim S.',
                'phone' => '+225 01 45 67 89 33',
                'rating' => 4.90,
                'trips_count' => 1521,
                'car_make' => 'Toyota Fortuner',
                'car_color_fr' => 'gris foncé',
                'car_color_en' => 'dark grey',
                'plate' => '9034 DF 01',
                'lat' => 5.3570,
                'lng' => -3.9920,
            ],
            [
                'slug' => 'moto',
                'name' => 'Konan M.',
                'phone' => '+225 07 99 00 11 02',
                'rating' => 4.81,
                'trips_count' => 2310,
                'car_make' => 'Yamaha Crux',
                'car_color_fr' => 'orange',
                'car_color_en' => 'orange',
                'plate' => 'M 4412 01',
                'lat' => 5.3559,
                'lng' => -3.9869,
            ],
        ];

        foreach ($drivers as $data) {
            $driver = Driver::updateOrCreate(
                ['plate' => $data['plate']],
                [
                    'vehicle_class_id' => $classes[$data['slug']],
                    'name' => $data['name'],
                    'avatar_initial' => mb_substr($data['name'], 0, 1),
                    'phone' => $data['phone'],
                    'rating' => $data['rating'],
                    'trips_count' => $data['trips_count'],
                    'car_make' => $data['car_make'],
                    'car_color_fr' => $data['car_color_fr'],
                    'car_color_en' => $data['car_color_en'],
                    'is_available' => true,
                    'current_lat' => $data['lat'],
                    'current_lng' => $data['lng'],
                ],
            );

            $locations->markAvailable($driver);
        }
    }
}
