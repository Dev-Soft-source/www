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
        Schema::table('my_vehicle_setting_detail', function (Blueprint $table) {
            $table->string('cannot_delete_vehicle_upcoming_ride_message', 500)->nullable()->after('delete_vehicle_message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_vehicle_setting_detail', function (Blueprint $table) {
            $table->dropColumn('cannot_delete_vehicle_upcoming_ride_message');
        });
    }
};
