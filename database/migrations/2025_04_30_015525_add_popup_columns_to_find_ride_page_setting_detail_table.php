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
            $table->text('hide_ride_popup_heading')->nullable();
            $table->text('hide_ride_popup_text')->nullable();
            $table->text('hide_ride_popup_confirm_button')->nullable();
            $table->text('hide_ride_popup_take_me_back_button')->nullable();
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
            $table->dropColumn('hide_ride_popup_heading');
            $table->dropColumn('hide_ride_popup_text');
            $table->dropColumn('hide_ride_popup_confirm_button');
            $table->dropColumn('hide_ride_popup_take_me_back_button');
        });
    }
};
