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
            $table->text('driver_remove_reason_error')->nullable();
            $table->text('passenger_remove_reason_error')->nullable();
            $table->text('remove_day_error')->nullable();
            $table->text('remove_passenger_heading')->nullable();
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
            $table->dropColumn(['remove_passenger_heading', 'remove_day_error', 'passenger_remove_reason_error', 'driver_remove_reason_error']);
        });
    }
};
