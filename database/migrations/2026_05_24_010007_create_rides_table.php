<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_class_id')->constrained();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status')->default('searching'); // RideStatus
            $table->string('payment_type');                  // PaymentType

            // Pickup
            $table->string('pickup_name');
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_lng', 10, 7);
            // Dropoff
            $table->string('dropoff_name');
            $table->decimal('dropoff_lat', 10, 7);
            $table->decimal('dropoff_lng', 10, 7);

            // Trip metrics
            $table->decimal('distance_km', 6, 2)->default(0);
            $table->unsignedSmallInteger('duration_minutes')->default(0);

            // Fare breakdown (F CFA integers)
            $table->unsignedInteger('base_fare')->default(0);
            $table->unsignedInteger('airport_fee')->default(0);
            $table->unsignedInteger('tip')->default(0);
            $table->unsignedInteger('total_fare')->default(0);

            // Lifecycle timestamps
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
