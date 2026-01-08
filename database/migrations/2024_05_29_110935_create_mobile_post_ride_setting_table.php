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
        Schema::create('mobile_post_ride_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('mobile_post_ride_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_ride_setting_id')
                ->constrained()
                ->on('mobile_post_ride_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('main_heading')->nullable();
            $table->string('post_arrived_again_label')->nullable();
            $table->string('ride_info_heading')->nullable();
            $table->string('from_label')->nullable();
            $table->string('from_placeholder')->nullable();
            $table->string('to_label')->nullable();
            $table->string('to_placeholder')->nullable();
            $table->string('pick_up_label')->nullable();
            $table->string('pick_up_placeholder')->nullable();
            $table->string('drop_off_label')->nullable();
            $table->string('drop_off_placeholder')->nullable();
            $table->string('date_time_label')->nullable();
            $table->string('at_label')->nullable();
            $table->string('recurring_label')->nullable();
            $table->string('recurring_type_label')->nullable();
            $table->string('recurring_trips_label')->nullable();
            $table->string('meeting_drop_off_description_label')->nullable();
            $table->text('meeting_drop_off_description_placeholder')->nullable();
            $table->string('seats_label')->nullable();
            $table->string('seats_middle_label')->nullable();
            $table->string('seats_back_label')->nullable();
            $table->string('vehicle_label')->nullable();
            $table->string('skip_label')->nullable();
            $table->string('add_vehicle_label')->nullable();
            $table->string('make_label')->nullable();
            $table->string('make_placeholder')->nullable();
            $table->string('model_label')->nullable();
            $table->string('model_placeholder')->nullable();
            $table->string('type_label')->nullable();
            $table->string('year_label')->nullable();
            $table->string('color_label')->nullable();
            $table->string('liscense_label')->nullable();
            $table->string('electric_car_label')->nullable();
            $table->string('hybrid_car_label')->nullable();
            $table->string('car_photo_label')->nullable();
            $table->string('smoking_label')->nullable();
            $table->string('animals_label')->nullable();
            $table->string('preferences_label')->nullable();
            $table->string('booking_label')->nullable();
            $table->string('booking_option1')->nullable();
            $table->string('booking_option2')->nullable();
            $table->string('luggage_label')->nullable();
            $table->text('luggage_checkbox_label1')->nullable();
            $table->text('luggage_checkbox_label2')->nullable();
            $table->string('price_payment_heading')->nullable();
            $table->string('price_per_seat_label')->nullable();
            $table->string('payment_methods_label')->nullable();
            $table->string('anything_to_add_label')->nullable();
            $table->text('anything_to_add_placeholder')->nullable();
            $table->string('disclaimers_label')->nullable();
            $table->longText('disclaimers_description')->nullable();
            $table->text('agree_terms_label')->nullable();
            $table->string('submit_button_label')->nullable();
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
        Schema::dropIfExists('mobile_post_ride_setting_detail');
        Schema::dropIfExists('mobile_post_ride_setting');
    }
};
