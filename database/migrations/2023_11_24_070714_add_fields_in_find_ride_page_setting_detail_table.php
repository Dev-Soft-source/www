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
            $table->string('ride_features_option15')->after('ride_features_option14')->nullable();
            $table->string('ride_features_option16')->after('ride_features_option15')->nullable();
            $table->string('ride_features_option17')->after('ride_features_option16')->nullable();
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
            $table->dropColumn(['ride_features_option16','ride_features_option15']);
        });
    }
};
