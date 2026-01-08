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
            $table->string('post_arrived_again_label')->after('main_heading')->nullable();
            $table->string('ride_info_heading')->after('post_arrived_again_label')->nullable();
            $table->string('add_vehicle_label')->after('skip_label')->nullable();
            $table->string('price_payment_heading')->after('luggage_checkbox_label2')->nullable();
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
            $table->dropColumn(['post_arrived_again_label','ride_info_heading','add_vehicle_label','price_payment_heading']);
        });
    }
};
