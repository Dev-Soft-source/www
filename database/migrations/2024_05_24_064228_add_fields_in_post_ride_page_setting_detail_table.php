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
            $table->string('luggage_option5_label')->after('luggage_option5')->nullable();
        });
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->string('luggage_option5_label')->after('luggage_option5')->nullable();
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
            $table->dropColumn(['luggage_option5_label']);
        });
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['luggage_option5_label']);
        });
    }
};
