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
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->text('timeliness_label')->nullable();
            $table->text('safety_label')->nullable();
            $table->text('respect_and_courtesy_label')->nullable();
            $table->text('personal_hygiene_label')->nullable();
            $table->text('overall_attitude_label')->nullable();
            $table->text('communication_label')->nullable();
            $table->text('comfort_label')->nullable();
            $table->text('conscious_passenger_wellness_label')->nullable();
            $table->text('condition_label')->nullable();
            $table->text('review_criteria_label')->nullable();
            $table->text('main_heading')->nullable();
            $table->text('average_label')->nullable();
            $table->text('load_more_trips_label')->nullable();
            $table->text('no_more_data_message')->nullable();
            $table->text('load_more_rides_label')->nullable();
            $table->text('review_passengers_review_label')->nullable();
            $table->text('review_passengers_i_review_label')->nullable();
            $table->text('review_passengers_heading')->nullable();
            $table->text('booking_cancel_btn_label')->nullable();
            $table->text('cancel_booking_trip_placeholder')->nullable();
            $table->text('cancel_ride_placeholder')->nullable();
            $table->text('cancel_seat_label')->nullable();
            $table->text('number_of_seat_booked')->nullable();
            $table->text('cancel_booking_heading')->nullable();
            $table->text('cancel_booking_main_heading')->nullable();
            $table->text('cancel_ride_setting')->nullable();
            $table->text('remove_from_this_ride_message')->nullable();
            $table->text('remove_passenger_and_block_message')->nullable();
            $table->text('remove_day_label')->nullable();
            $table->text('driver_remove_reason_placeholder')->nullable();
            $table->text('passenger_remove_reason_placeholder')->nullable();
            $table->text('passenger_cancel_ride_btn_label')->nullable();
            $table->text('passenger_review_heading')->nullable();
            $table->text('driver_review_heading')->nullable();
            $table->text('passenger_review_placeholder')->nullable();
            $table->text('driver_review_placeholder')->nullable();
            $table->text('review_submit_btn_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'timeliness_label',
                'safety_label',
                'respect_and_courtesy_label',
                'personal_hygiene_label',
                'overall_attitude_label',
                'communication_label',
                'comfort_label',
                'conscious_passenger_wellness_label',
                'condition_label',
                'review_criteria_label',
                'main_heading',
                'average_label',
                'load_more_trips_label',
                'no_more_data_message',
                'load_more_rides_label',
                'review_passengers_review_label',
                'review_passengers_i_review_label',
                'review_passengers_heading',
                'booking_cancel_btn_label',
                'cancel_booking_trip_placeholder',
                'cancel_ride_placeholder',
                'cancel_seat_label',
                'number_of_seat_booked',
                'cancel_booking_heading',
                'cancel_booking_main_heading',
                'cancel_ride_setting',
                'remove_from_this_ride_message',
                'remove_passenger_and_block_message',
                'remove_day_label',
                'driver_remove_reason_placeholder',
                'passenger_remove_reason_placeholder',
                'passenger_cancel_ride_btn_label',
                'passenger_review_heading',
                'driver_review_heading',
                'passenger_review_placeholder',
                'driver_review_placeholder',
                'review_submit_btn_label',
            ]);
        });
    }
};
