<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_classes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();            // mini, confort, xl, moto
            $table->string('name');                       // Babi Mini
            $table->string('description_fr');
            $table->string('description_en');
            $table->unsignedTinyInteger('seats');
            $table->unsignedSmallInteger('default_eta_minutes');
            // Fare model (amounts in F CFA, integer)
            $table->unsignedInteger('booking_fee');
            $table->unsignedInteger('per_km');
            $table->unsignedInteger('per_minute');
            $table->unsignedInteger('min_fare');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_classes');
    }
};
