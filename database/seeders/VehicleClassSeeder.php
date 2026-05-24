<?php

namespace Database\Seeders;

use App\Models\VehicleClass;
use Illuminate\Database\Seeder;

class VehicleClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            [
                'slug' => 'moto',
                'name' => 'Babi Moto',
                'description_fr' => 'Rapide',
                'description_en' => 'Quickest',
                'seats' => 1,
                'default_eta_minutes' => 2,
                'booking_fee' => 250,
                'per_km' => 55,
                'per_minute' => 6,
                'min_fare' => 800,
                'sort_order' => 1,
            ],
            [
                'slug' => 'mini',
                'name' => 'Babi Mini',
                'description_fr' => 'Économique',
                'description_en' => 'Affordable',
                'seats' => 3,
                'default_eta_minutes' => 3,
                'booking_fee' => 350,
                'per_km' => 78,
                'per_minute' => 8,
                'min_fare' => 1000,
                'sort_order' => 2,
            ],
            [
                'slug' => 'confort',
                'name' => 'Babi Confort',
                'description_fr' => 'Berline · clim',
                'description_en' => 'Sedan · AC',
                'seats' => 4,
                'default_eta_minutes' => 4,
                'booking_fee' => 500,
                'per_km' => 112,
                'per_minute' => 10,
                'min_fare' => 1500,
                'sort_order' => 3,
            ],
            [
                'slug' => 'xl',
                'name' => 'Babi XL',
                'description_fr' => 'Groupe · SUV',
                'description_en' => 'Group · SUV',
                'seats' => 6,
                'default_eta_minutes' => 6,
                'booking_fee' => 800,
                'per_km' => 165,
                'per_minute' => 15,
                'min_fare' => 2200,
                'sort_order' => 4,
            ],
        ];

        foreach ($classes as $class) {
            VehicleClass::updateOrCreate(['slug' => $class['slug']], $class);
        }
    }
}
