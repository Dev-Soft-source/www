<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_ride_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('px_rides')->cascadeOnDelete();
            $table->unsignedTinyInteger('stop_order');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('label', 160);
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamp('eta_at')->nullable();
            $table->integer('price_delta_minor')->default(0);
            $table->unsignedTinyInteger('seats_available')->nullable();
            $table->boolean('is_pickup')->default(true);
            $table->boolean('is_dropoff')->default(true);
            $table->timestamps();

            $table->unique(['ride_id', 'stop_order'], 'px_ride_stops_ride_order_uq');
            $table->index(['city_id'], 'px_ride_stops_city_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('px_ride_stops');
    }
};

