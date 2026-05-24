<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        $places = [
            [
                'name' => 'Aéroport Félix-Houphouët-Boigny',
                'subtitle' => 'Port-Bouët • 14.2 km',
                'icon' => 'plane',
                'neighborhood' => 'Port-Bouët',
                'lat' => 5.2614000,
                'lng' => -3.9263000,
                'is_airport' => true,
                'popularity' => 100,
            ],
            [
                'name' => 'CHU de Cocody',
                'subtitle' => 'Cocody • Boulevard de France',
                'icon' => 'pin',
                'neighborhood' => 'Cocody',
                'lat' => 5.3490000,
                'lng' => -3.9850000,
                'popularity' => 60,
            ],
            [
                'name' => 'Marché de Treichville',
                'subtitle' => 'Treichville • Av. 21',
                'icon' => 'pin',
                'neighborhood' => 'Treichville',
                'lat' => 5.2950000,
                'lng' => -4.0080000,
                'popularity' => 55,
            ],
            [
                'name' => 'Palais de la Culture',
                'subtitle' => 'Treichville • Boulevard de Marseille',
                'icon' => 'pin',
                'neighborhood' => 'Treichville',
                'lat' => 5.2920000,
                'lng' => -4.0150000,
                'popularity' => 50,
            ],
            [
                'name' => 'Sofitel Hôtel Ivoire',
                'subtitle' => 'Cocody • Bd Hassan II',
                'icon' => 'star',
                'neighborhood' => 'Cocody',
                'lat' => 5.3300000,
                'lng' => -3.9970000,
                'popularity' => 75,
            ],
            [
                'name' => 'PlaYce Marcory',
                'subtitle' => "Marcory • Bd Valéry Giscard d'Estaing",
                'icon' => 'shop',
                'neighborhood' => 'Marcory',
                'lat' => 5.3010000,
                'lng' => -3.9880000,
                'popularity' => 70,
            ],
            [
                'name' => 'Plateau · Avenue Chardy',
                'subtitle' => 'Plateau • Centre des affaires',
                'icon' => 'work',
                'neighborhood' => 'Plateau',
                'lat' => 5.3220000,
                'lng' => -4.0190000,
                'popularity' => 65,
            ],
            [
                'name' => 'Cocody · Riviera Golf',
                'subtitle' => 'Cocody • Riviera',
                'icon' => 'home',
                'neighborhood' => 'Cocody',
                'lat' => 5.3560000,
                'lng' => -3.9870000,
                'popularity' => 80,
            ],
        ];

        foreach ($places as $place) {
            Place::updateOrCreate(['name' => $place['name']], $place);
        }
    }
}
