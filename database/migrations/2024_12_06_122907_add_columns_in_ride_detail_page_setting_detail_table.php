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
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('noon_label')->nullable();
            $table->text('midnight_label')->nullable();
            $table->text('booking_request_heading')->nullable();
            $table->text('review_label')->nullable();
            $table->text('seat_requested_label')->nullable();
            $table->text('request_accept_label')->nullable();
            $table->text('request_reject_label')->nullable();
            $table->text('enter_code_label')->nullable();
            $table->text('secured_cash_heading')->nullable();
            $table->text('mobile_seat_booked_heading')->nullable();
            $table->text('mobile_seat_booked_label')->nullable();
            $table->text('mobile_seat_fare_label')->nullable();
            $table->text('mobile_seat_booking_fee_label')->nullable();
            $table->text('mobile_seat_total_amount_label')->nullable();
            $table->text('ride_seat_label')->nullable();
            $table->text('trip_co_passenger_heading')->nullable();
            $table->text('ride_co_passenger_heading')->nullable();
            $table->text('edit_ride_btn_label')->nullable();
            $table->text('cancel_ride_btn_label')->nullable();
            $table->text('no_seat_available_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['no_seat_available_label', 'cancel_ride_btn_label', 'edit_ride_btn_label', 'ride_co_passenger_heading', 'trip_co_passenger_heading', 'ride_seat_label', 'mobile_seat_total_amount_label',
                'mobile_seat_booking_fee_label', 'mobile_seat_fare_label', 'mobile_seat_booked_label', 'mobile_seat_booked_heading',
                'secured_cash_heading', 'enter_code_label', 'request_reject_label', 'request_accept_label', 'seat_requested_label',
                'review_label', 'booking_request_heading', 'midnight_label', 'noon_label'
            ]);
        });
    }
};
