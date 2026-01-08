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
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('cancel_ride_yes_btn')->nullable();
            $table->text('cancel_ride_no_btn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['cancel_ride_no_btn', 'cancel_ride_yes_btn']);
        });
    }
};
