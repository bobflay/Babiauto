<?php

namespace Tests\Feature;

use App\Enums\RideStatus;
use App\Models\Driver;
use App\Models\User;
use App\Models\VehicleClass;
use Database\Seeders\VehicleClassSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RideFlowTest extends TestCase
{
    use RefreshDatabase;

    private function pickupDropoff(): array
    {
        return [
            'pickup' => ['name' => 'Cocody · Riviera Golf', 'lat' => 5.3560, 'lng' => -3.9870],
            'dropoff' => ['name' => 'Aéroport FHB', 'lat' => 5.2614, 'lng' => -3.9263],
        ];
    }

    private function seedClassAndDriver(): VehicleClass
    {
        $this->seed(VehicleClassSeeder::class);
        $confort = VehicleClass::where('slug', 'confort')->first();
        Driver::factory()->forClass($confort)->create([
            'name' => 'Kouassi A.',
            'current_lat' => 5.3548,
            'current_lng' => -3.9881,
        ]);

        return $confort;
    }

    public function test_requesting_a_ride_matches_the_nearest_driver(): void
    {
        $this->seedClassAndDriver();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/rides', [
                'vehicle_class' => 'confort',
                'payment_type' => 'mobile',
            ] + $this->pickupDropoff());

        $response->assertCreated()
            ->assertJsonPath('data.status', RideStatus::Accepted->value)
            ->assertJsonPath('data.driver.name', 'Kouassi A.');

        $this->assertDatabaseHas('drivers', ['name' => 'Kouassi A.', 'is_available' => false]);
    }

    public function test_full_lifecycle_through_completion_and_rating(): void
    {
        $this->seedClassAndDriver();
        $user = User::factory()->create();

        $rideId = $this->actingAs($user)
            ->postJson('/api/v1/rides', ['vehicle_class' => 'confort', 'payment_type' => 'cash'] + $this->pickupDropoff())
            ->json('data.id');

        foreach (['arriving' => RideStatus::Arriving, 'arrived' => RideStatus::Arrived, 'start' => RideStatus::InProgress, 'complete' => RideStatus::Completed] as $action => $status) {
            $this->actingAs($user)
                ->postJson("/api/v1/rides/{$rideId}/{$action}")
                ->assertOk()
                ->assertJsonPath('data.status', $status->value);
        }

        $this->actingAs($user)
            ->postJson("/api/v1/rides/{$rideId}/rate", ['stars' => 5, 'tip' => 500])
            ->assertOk()
            ->assertJsonPath('data.fare.tip', 500);

        $this->assertDatabaseHas('ride_ratings', ['ride_id' => $rideId, 'stars' => 5]);
        $this->assertDatabaseHas('drivers', ['name' => 'Kouassi A.', 'is_available' => true]);
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $this->seedClassAndDriver();
        $user = User::factory()->create();

        $rideId = $this->actingAs($user)
            ->postJson('/api/v1/rides', ['vehicle_class' => 'confort', 'payment_type' => 'cash'] + $this->pickupDropoff())
            ->json('data.id');

        // Cannot complete directly from "accepted".
        $this->actingAs($user)
            ->postJson("/api/v1/rides/{$rideId}/complete")
            ->assertStatus(422);
    }

    public function test_a_ride_can_be_cancelled_and_frees_the_driver(): void
    {
        $this->seedClassAndDriver();
        $user = User::factory()->create();

        $rideId = $this->actingAs($user)
            ->postJson('/api/v1/rides', ['vehicle_class' => 'confort', 'payment_type' => 'cash'] + $this->pickupDropoff())
            ->json('data.id');

        $this->actingAs($user)
            ->postJson("/api/v1/rides/{$rideId}/cancel", ['reason' => 'changed plans'])
            ->assertOk()
            ->assertJsonPath('data.status', RideStatus::Cancelled->value);

        $this->assertDatabaseHas('drivers', ['name' => 'Kouassi A.', 'is_available' => true]);
    }

    public function test_a_user_cannot_view_another_users_ride(): void
    {
        $this->seedClassAndDriver();
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $rideId = $this->actingAs($owner)
            ->postJson('/api/v1/rides', ['vehicle_class' => 'confort', 'payment_type' => 'cash'] + $this->pickupDropoff())
            ->json('data.id');

        $this->actingAs($intruder)
            ->getJson("/api/v1/rides/{$rideId}")
            ->assertForbidden();
    }
}
