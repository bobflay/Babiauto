<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\VehicleClass;
use Database\Seeders\VehicleClassSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class NearbyDriverTest extends TestCase
{
    use RefreshDatabase;

    private float $lat = 5.3560;

    private float $lng = -3.9870;

    private function seedFleet(): void
    {
        $this->seed(VehicleClassSeeder::class);
        $confort = VehicleClass::where('slug', 'confort')->first();
        $moto = VehicleClass::where('slug', 'moto')->first();

        Driver::factory()->count(2)->forClass($confort)->create();
        Driver::factory()->forClass($moto)->create();
    }

    public function test_it_returns_available_drivers_around_the_rider(): void
    {
        config(['babiauto.simulate_drivers' => true]);
        $this->seedFleet();

        $response = $this->getJson("/api/v1/drivers/nearby?lat={$this->lat}&lng={$this->lng}");

        $response->assertOk()
            ->assertJsonPath('meta.simulated', true)
            ->assertJsonPath('meta.count', 3)
            ->assertJsonStructure([
                'data' => [['id', 'vehicle_class', 'lat', 'lng', 'heading', 'distance_km', 'eta_minutes', 'simulated']],
                'meta' => ['simulated', 'count', 'center'],
            ]);
    }

    public function test_simulated_drivers_move_between_polls(): void
    {
        config(['babiauto.simulate_drivers' => true]);
        $this->seedFleet();

        Carbon::setTestNow(Carbon::createFromTimestamp(1_000_000));
        $first = $this->getJson("/api/v1/drivers/nearby?lat={$this->lat}&lng={$this->lng}")->json('data.0');

        Carbon::setTestNow(Carbon::createFromTimestamp(1_000_030));
        $later = $this->getJson("/api/v1/drivers/nearby?lat={$this->lat}&lng={$this->lng}")->json('data.0');

        Carbon::setTestNow();

        $this->assertSame($first['id'], $later['id']);
        $this->assertNotEquals(
            [$first['lat'], $first['lng']],
            [$later['lat'], $later['lng']],
            'Simulated driver position should change over time.'
        );
    }

    public function test_it_filters_by_vehicle_class(): void
    {
        config(['babiauto.simulate_drivers' => true]);
        $this->seedFleet();

        $response = $this->getJson("/api/v1/drivers/nearby?lat={$this->lat}&lng={$this->lng}&vehicle_class=confort");

        $response->assertOk()->assertJsonPath('meta.count', 2);
        foreach ($response->json('data') as $car) {
            $this->assertSame('confort', $car['vehicle_class']);
        }
    }

    public function test_when_simulation_is_off_it_returns_real_positions_within_radius(): void
    {
        config(['babiauto.simulate_drivers' => false, 'babiauto.match_radius_km' => 8]);
        $this->seed(VehicleClassSeeder::class);
        $confort = VehicleClass::where('slug', 'confort')->first();

        $near = Driver::factory()->forClass($confort)->create(['current_lat' => 5.3548, 'current_lng' => -3.9881]);
        Driver::factory()->forClass($confort)->create(['current_lat' => 6.5000, 'current_lng' => -5.0000]); // far away

        $response = $this->getJson("/api/v1/drivers/nearby?lat={$this->lat}&lng={$this->lng}");

        $response->assertOk()
            ->assertJsonPath('meta.simulated', false)
            ->assertJsonPath('meta.count', 1)
            ->assertJsonPath('data.0.id', $near->id)
            ->assertJsonPath('data.0.heading', null);
    }

    public function test_it_requires_coordinates(): void
    {
        $this->getJson('/api/v1/drivers/nearby?lng=-3.98')->assertStatus(422);
    }
}
