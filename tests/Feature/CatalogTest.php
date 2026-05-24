<?php

namespace Tests\Feature;

use App\Models\Place;
use Database\Seeders\VehicleClassSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_classes_are_listed_sorted(): void
    {
        $this->seed(VehicleClassSeeder::class);

        $this->getJson('/api/v1/vehicle-classes')
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data.0.slug', 'moto');
    }

    public function test_places_can_be_searched(): void
    {
        Place::create([
            'name' => 'Aéroport Félix-Houphouët-Boigny',
            'subtitle' => 'Port-Bouët',
            'icon' => 'plane',
            'neighborhood' => 'Port-Bouët',
            'lat' => 5.2614,
            'lng' => -3.9263,
            'is_airport' => true,
            'popularity' => 100,
        ]);
        Place::create([
            'name' => 'Sofitel Hôtel Ivoire',
            'icon' => 'star',
            'neighborhood' => 'Cocody',
            'lat' => 5.33,
            'lng' => -3.997,
        ]);

        $this->getJson('/api/v1/places?q=aéro')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.is_airport', true);
    }

    public function test_fare_estimate_returns_a_quote_per_class_with_airport_fee(): void
    {
        $this->seed(VehicleClassSeeder::class);
        Place::create([
            'name' => 'Aéroport FHB',
            'icon' => 'plane',
            'lat' => 5.2614,
            'lng' => -3.9263,
            'is_airport' => true,
        ]);

        $response = $this->postJson('/api/v1/rides/estimate', [
            'pickup' => ['lat' => 5.3560, 'lng' => -3.9870],
            'dropoff' => ['lat' => 5.2614, 'lng' => -3.9263],
        ]);

        $response->assertOk()->assertJsonCount(4, 'data');
        $this->assertSame(400, $response->json('data.0.airport_fee'));
        $this->assertGreaterThan(0, $response->json('data.0.total_fare'));
    }
}
