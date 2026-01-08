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
            $table->text('extra_care_ride_page_label')->nullable();
            $table->text('search_results_extra_care_ride_label')->nullable();
            $table->text('imp_extra_care_ride_label')->nullable();
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
            $table->dropColumn(['imp_extra_care_ride_label', 'search_results_extra_care_ride_label', 'extra_care_ride_page_label']);
        });
    }
};
