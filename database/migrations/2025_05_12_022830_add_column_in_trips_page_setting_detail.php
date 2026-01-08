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
            $table->text('tell_passenger_why_label')->nullable();
            $table->text('tell_passenger_why_placeholder')->nullable();
            $table->text('Confirm_cancel_ride')->nullable();
            $table->text('cancel_ride_label')->nullable();
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
            $table->dropColumn(['cancel_ride_label', 'Confirm_cancel_ride', 'tell_passenger_why_placeholder', 'tell_passenger_why_label']);
        });
    }
};
