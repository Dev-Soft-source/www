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
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->string('make_error')->nullable();
            $table->string('model_error')->nullable();
            $table->string('vehicle_type_error')->nullable();
            $table->string('color_error')->nullable();
            $table->string('license_error')->nullable();
            $table->string('year_error')->nullable();
            $table->string('fuel_error')->nullable();
            $table->string('driver_license_error')->nullable();
            $table->string('photo_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['photo_error', 'driver_license_error', 'fuel_error', 'year_error', 'license_error',
                'color_error', 'vehicle_type_error', 'model_error', 'make_error'
            ]);
        });
    }
};
