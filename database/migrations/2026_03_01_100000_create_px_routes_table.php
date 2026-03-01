<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('destination_city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('origin_label', 160);
            $table->string('destination_label', 160);
            $table->decimal('origin_lat', 10, 7)->nullable();
            $table->decimal('origin_lng', 10, 7)->nullable();
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();
            $table->char('origin_geohash', 12)->nullable();
            $table->char('destination_geohash', 12)->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->text('polyline')->nullable();
            $table->string('fingerprint', 64)->unique();
            $table->timestamps();

            $table->index(['origin_city_id', 'destination_city_id'], 'px_routes_city_pair_idx');
            $table->index(['origin_geohash'], 'px_routes_origin_geohash_idx');
            $table->index(['destination_geohash'], 'px_routes_destination_geohash_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('px_routes');
    }
};

