<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Aéroport Félix-Houphouët-Boigny
            $table->string('subtitle')->nullable();     // Port-Bouët • 14.2 km
            $table->string('icon')->default('pin');     // plane, pin, star, shop, home, work
            $table->string('neighborhood')->nullable(); // Cocody, Plateau...
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->boolean('is_airport')->default(false);
            $table->unsignedInteger('popularity')->default(0);
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
