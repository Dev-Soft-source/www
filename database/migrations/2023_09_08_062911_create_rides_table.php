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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->string('departure');
            $table->string('departure_lat');
            $table->string('departure_lng');
            $table->string('destination');
            $table->string('destination_lat');
            $table->string('destination_lng');
            $table->string('total_distance');
            $table->string('total_time');
            $table->date('date');
            $table->time('time');
            $table->string('recurring');
            $table->string('details');
            $table->string('seats');
            $table->string('model');
            $table->string('vehicle_type');
            $table->string('year');
            $table->string('color');
            $table->string('license_no');
            $table->string('smoke');
            $table->string('animal_friendly');
            $table->string('features');
            $table->string('booking_method');
            $table->string('max_back_seats');
            $table->string('luggage');
            $table->string('accept_more_luggage');
            $table->string('open_customized')->nullable();
            $table->string('price');
            $table->string('payment_method');
            $table->string('notes');
            $table->foreignId('added_by')
                  ->constrained()
                  ->on('users')
                  ->references('id')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->timestamp('added_on')->useCurrent();
            $table->string('url');
            $table->string('departure_place');
            $table->string('departure_route');
            $table->string('departure_zipcode');
            $table->string('departure_city');
            $table->string('departure_state');
            $table->string('departure_country');
            $table->string('destination_place');
            $table->string('destination_route');
            $table->string('destination_zipcode');
            $table->string('destination_city');
            $table->string('destination_state');
            $table->string('destination_country');
            $table->string('car_image')->nullable();
            $table->string('skip_vehicle')->default('0');
            $table->string('status')->default('0');
            $table->date('until_date')->nullable();
            $table->string('until_limit');
            $table->string('repeated')->default('0');
            $table->date('last_repeated')->nullable();
            $table->string('parent')->default('0');
            $table->string('pickup');
            $table->string('dropoff');
            $table->string('departure_state_short');
            $table->string('destination_state_short');
            $table->string('closed')->default('0');
            $table->string('suspand')->default('0');
            $table->string('middle_seats');
            $table->string('back_seats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rides');
    }
};
