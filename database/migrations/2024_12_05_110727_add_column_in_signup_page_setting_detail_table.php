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
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->string('language_label')->nullable();
        });
        Schema::table('login_page_setting_detail', function (Blueprint $table) {
            $table->string('language_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['language_label']);
        });
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['language_label']);
        });
    }
};
