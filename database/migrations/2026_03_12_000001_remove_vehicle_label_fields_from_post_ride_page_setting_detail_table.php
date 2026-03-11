<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'make_label',
                'model_label',
                'type_label',
                'year_label',
                'color_label',
                'liscense_label',
                'car_type_label',
                'electric_car_label',
                'hybrid_car_label',
                'gas_car_label',
            ]);
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('make_label')->nullable();
            $table->text('model_label')->nullable();
            $table->text('type_label')->nullable();
            $table->text('year_label')->nullable();
            $table->text('color_label')->nullable();
            $table->text('liscense_label')->nullable();
            $table->text('car_type_label')->nullable();
            $table->text('electric_car_label')->nullable();
            $table->text('hybrid_car_label')->nullable();
            $table->text('gas_car_label')->nullable();
        });
    }
};
