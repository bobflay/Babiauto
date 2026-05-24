<?php

namespace Database\Seeders;

use App\Enums\PaymentType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'koffi@babiauto.ci'],
            [
                'name' => 'Koffi',
                'phone' => '+225 07 00 00 00 01',
                'password' => Hash::make('password'),
                'language' => 'fr',
                'avatar_initial' => 'K',
            ],
        );

        $savedPlaces = [
            [
                'label' => 'Maison',
                'icon' => 'home',
                'name' => 'Cocody · Riviera 3',
                'subtitle' => 'Cocody • Riviera',
                'lat' => 5.3560000,
                'lng' => -3.9870000,
            ],
            [
                'label' => 'Bureau',
                'icon' => 'work',
                'name' => 'Plateau · Av. Chardy',
                'subtitle' => 'Plateau • Centre des affaires',
                'lat' => 5.3220000,
                'lng' => -4.0190000,
            ],
        ];

        foreach ($savedPlaces as $place) {
            $user->savedPlaces()->updateOrCreate(['label' => $place['label']], $place);
        }

        $paymentMethods = [
            ['type' => PaymentType::Mobile, 'provider' => 'Orange', 'last4' => null, 'is_default' => true],
            ['type' => PaymentType::Card, 'provider' => 'Visa', 'last4' => '4127', 'is_default' => false],
            ['type' => PaymentType::Cash, 'provider' => null, 'last4' => null, 'is_default' => false],
        ];

        foreach ($paymentMethods as $method) {
            $user->paymentMethods()->updateOrCreate(
                ['type' => $method['type']],
                $method,
            );
        }
    }
}
