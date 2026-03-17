<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_stop_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')
                ->constrained('rides')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('from_stop_id')
                ->constrained('ride_stops')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('to_stop_id')
                ->constrained('ride_stops')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->integer('price_minor')->default(0);
            $table->integer('distance_meters')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();

            $table->unique(['ride_id', 'from_stop_id', 'to_stop_id'], 'ride_stop_segments_unique');
            $table->index(['ride_id', 'from_stop_id'], 'ride_stop_segments_from_idx');
            $table->index(['ride_id', 'to_stop_id'], 'ride_stop_segments_to_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ride_stop_segments');
    }
};
