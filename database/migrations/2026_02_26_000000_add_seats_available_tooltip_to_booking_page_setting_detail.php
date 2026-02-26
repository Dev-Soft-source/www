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
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('seats_available_tooltip')->nullable();
            $table->text('chat_with_driver_tooltip')->nullable();
            $table->text('aggreement_tooltip')->nullable();
            $table->text('pink_ride_tooltip')->nullable();
            $table->text('extra_care_ride_tooltip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['seats_available_tooltip', 'chat_with_driver_tooltip', 'aggreement_tooltip', 'pink_ride_tooltip', 'extra_care_ride_tooltip']);
        });
    }
};
