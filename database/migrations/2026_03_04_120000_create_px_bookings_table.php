<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('px_rides')->cascadeOnDelete();
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_stop_id')->constrained('px_ride_stops')->cascadeOnDelete();
            $table->foreignId('to_stop_id')->constrained('px_ride_stops')->cascadeOnDelete();
            $table->foreignId('card_id')->nullable()->constrained('cards')->nullOnDelete();

            $table->unsignedTinyInteger('seats');
            $table->unsignedInteger('segment_price_minor');
            $table->unsignedInteger('total_price_minor');
            $table->char('currency', 3)->default('CAD');

            $table->string('status', 32)->default('paid');
            $table->timestamp('booked_at')->useCurrent();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['ride_id', 'status'], 'px_bookings_ride_status_idx');
            $table->index(['passenger_id', 'booked_at'], 'px_bookings_passenger_time_idx');
            $table->index(['driver_id', 'booked_at'], 'px_bookings_driver_time_idx');
            $table->index(['from_stop_id', 'to_stop_id'], 'px_bookings_segment_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('px_bookings');
    }
};

