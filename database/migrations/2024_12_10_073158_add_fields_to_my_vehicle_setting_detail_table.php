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
            $table->string('no_vehicle_message')->nullable();
            $table->string('update_vehicle_button_text')->nullable();
            $table->string('remove_car_photo_label')->nullable();
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
            $table->dropColumn('no_vehicle_message');
            $table->dropColumn('update_vehicle_button_text');
            $table->dropColumn('remove_car_photo_label');
        });
    }
};
