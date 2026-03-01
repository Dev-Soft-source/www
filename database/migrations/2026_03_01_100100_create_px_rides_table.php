<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('px_routes')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();

            $table->timestamp('departure_at')->index();
            $table->timestamp('arrival_estimated_at')->nullable();
            $table->unsignedSmallInteger('boarding_window_minutes')->default(15);

            $table->unsignedTinyInteger('seats_total');
            $table->unsignedTinyInteger('seats_available');
            $table->unsignedInteger('price_minor');
            $table->char('currency', 3)->default('USD');

            $table->enum('status', ['draft', 'published', 'started', 'completed', 'cancelled'])->default('draft')->index();
            $table->enum('visibility', ['public', 'private'])->default('public');
            
            $table->boolean('allow_detour')->default(false);
            $table->boolean('women_only')->default(false);
            $table->boolean('extra_care')->default(false);
            
            
            $table->integer('booking_mode')->default(0);
            $table->integer('booking_method')->default(0);
            $table->integer('smoking_allowed')->default(0);
            $table->integer('pets_allowed')->default(0);
            $table->integer('luggage_size')->default(0);
            $table->integer('cancelation_policy')->default(0);

            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'departure_at'], 'px_rides_status_departure_idx');
            $table->index(['driver_id', 'status', 'departure_at'], 'px_rides_driver_status_departure_idx');
            $table->index(['route_id', 'status', 'departure_at'], 'px_rides_route_status_departure_idx');
            $table->index(['status', 'visibility', 'departure_at', 'seats_available'], 'px_rides_feed_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('px_rides');
    }
};

