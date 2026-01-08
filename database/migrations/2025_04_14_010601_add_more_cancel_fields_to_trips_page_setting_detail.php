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
            $table->text('cancel_message_title')->nullable();
            $table->text('cancel_booking_confirm_message')->nullable();
            $table->text('booking_cancel_btn_yes_label')->nullable();
            $table->text('booking_cancel_btn_no_label')->nullable();
            $table->text('cancel_booking_confirm_firm_message')->nullable();
            $table->text('cancel_booking_confirm_48_hour_message')->nullable();
            $table->text('cancel_booking_confirm_12_to_48_hour_message')->nullable();
            $table->text('cancel_booking_confirm_less_12_hour_message')->nullable();
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
            $table->dropColumn(['cancel_message_title', 'cancel_booking_confirm_message', 'booking_cancel_btn_yes_label', 'booking_cancel_btn_no_label', 'cancel_booking_confirm_firm_message', 'cancel_booking_confirm_48_hour_message', 'cancel_booking_confirm_12_to_48_hour_message', 'cancel_booking_confirm_less_12_hour_message']);
        });
    }
};
