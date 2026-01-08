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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('vehicle_type_convertible_text')->nullable();
            $table->text('vehicle_type_hatchback_text')->nullable();
            $table->text('vehicle_type_coupe_text')->nullable();
            $table->text('vehicle_type_minivan_text')->nullable();
            $table->text('vehicle_type_sedan_text')->nullable();
            $table->text('vehicle_type_station_wagon_text')->nullable();
            $table->text('vehicle_type_suv_text')->nullable();
            $table->text('vehicle_type_truck_text')->nullable();
            $table->text('vehicle_type_van_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_type_convertible_text',
                'vehicle_type_hatchback_text',
                'vehicle_type_coupe_text',
                'vehicle_type_minivan_text',
                'vehicle_type_sedan_text',
                'vehicle_type_station_wagon_text',
                'vehicle_type_suv_text',
                'vehicle_type_truck_text',
                'vehicle_type_van_text',
            ]);
        });
    }
};
