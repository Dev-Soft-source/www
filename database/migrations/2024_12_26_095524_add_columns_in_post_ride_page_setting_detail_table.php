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
        Schema::create('post_ride_page_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_ride_page_setting_detail_id')
                ->constrained()
                ->on('post_ride_page_setting_detail')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('from_error')->nullable();
            $table->text('to_error')->nullable();
            $table->text('pick_up_error')->nullable();
            $table->text('drop_off_error')->nullable();
            $table->text('date_error')->nullable();
            $table->text('time_error')->nullable();
            $table->text('recurring_type_error')->nullable();
            $table->text('recurring_trips_error')->nullable();
            $table->text('meeting_drop_off_description_error')->nullable();
            $table->text('seats_error')->nullable();
            $table->text('seats_middle_error')->nullable();
            $table->text('seats_back_error')->nullable();
            $table->text('vehicle_id_error')->nullable();
            $table->text('make_error')->nullable();
            $table->text('model_error')->nullable();
            $table->text('vehicle_type_error')->nullable();
            $table->text('color_error')->nullable();
            $table->text('license_error')->nullable();
            $table->text('year_error')->nullable();
            $table->text('fuel_error')->nullable();
            $table->text('photo_error')->nullable();
            $table->text('booking_method_error')->nullable();
            $table->text('anything_to_add_error')->nullable();
            $table->text('smoking_error')->nullable();
            $table->text('animal_error')->nullable();
            $table->text('luggage_error')->nullable();
            $table->text('price_error')->nullable();
            $table->text('payment_method_error')->nullable();
            $table->text('booking_type_error')->nullable();
            $table->text('agree_terms_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_ride_page_errors');
    }
};
