<?php

namespace Tests\Feature;

use Database\Seeders\VehicleClassSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_landing_page_renders_with_vehicle_classes(): void
    {
        $this->seed(VehicleClassSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Babiauto')
            ->assertSee('Votre trajet')
            ->assertSee('Babi Confort')
            ->assertSee('F CFA');
    }
}
