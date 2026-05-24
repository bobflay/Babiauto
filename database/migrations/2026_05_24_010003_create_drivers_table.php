<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_class_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('avatar_initial', 2)->nullable();
            $table->string('phone');
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->unsignedInteger('trips_count')->default(0);
            // Vehicle details
            $table->string('car_make');                 // Suzuki Alto
            $table->string('car_color_fr');             // gris
            $table->string('car_color_en');             // grey
            $table->string('plate');                    // 2347 BG 01
            // Availability + last known position
            $table->boolean('is_available')->default(true);
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['vehicle_class_id', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
