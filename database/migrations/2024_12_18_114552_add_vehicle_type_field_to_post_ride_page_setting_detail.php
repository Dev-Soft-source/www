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
            $table->text('select_vehicle_type')->nullable();
            $table->text('vehicle_type_placeholder')->nullable();
            $table->text('seat_text')->nullable();
            $table->text('recurring_type_select_placeholder')->nullable();
            $table->text('recurring_type_daily_label')->nullable();
            $table->text('recurring_type_weekly_label')->nullable();
            $table->text('post_ride_again_main_heading')->nullable();
            $table->text('select_vehicle')->nullable();
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

                'select_vehicle_type', 'vehicle_type_placeholder', 'seat_text','recurring_type_select_placeholder','recurring_type_weekly_label',
                'recurring_type_daily_label','post_ride_again_main_heading','select_vehicle'
            ]);

        });
    }
};
