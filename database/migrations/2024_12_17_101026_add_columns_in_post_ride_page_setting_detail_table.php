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
            $table->string('luggage_checkbox_label1_tooltip')->after('luggage_checkbox_label1')->nullable();
            $table->string('luggage_checkbox_label2_tooltip')->after('luggage_checkbox_label2')->nullable();
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
            $table->dropColumn(['luggage_checkbox_label2_tooltip', 'luggage_checkbox_label1_tooltip']);
        });
    }
};
