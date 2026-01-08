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
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('pink_ride_page_heading')->nullable();
            $table->text('search_results_pink_ride_label')->nullable();
            $table->text('more_rides_pink_ride_label')->nullable();
            $table->text('to_pink_ride_label')->nullable();
            $table->text('imp_pink_ride_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['imp_pink_ride_label', 'to_pink_ride_label', 'more_rides_pink_ride_label', 'search_results_pink_ride_label', 'pink_ride_page_heading']);
        });
    }
};
