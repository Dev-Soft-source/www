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
        Schema::table('step1_page_setting_detail', function (Blueprint $table) {
            $table->string('logout_button_label')->nullable();
        });
        Schema::table('step2_page_setting_detail', function (Blueprint $table) {
            $table->string('logout_button_label')->nullable();
        });
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->string('logout_button_label')->nullable();
        });
        Schema::table('step4_page_setting_detail', function (Blueprint $table) {
            $table->string('logout_button_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('step4_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['logout_button_label']);
        });
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['logout_button_label']);
        });
        Schema::table('step2_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['logout_button_label']);
        });
        Schema::table('step1_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['logout_button_label']);
        });
    }
};
