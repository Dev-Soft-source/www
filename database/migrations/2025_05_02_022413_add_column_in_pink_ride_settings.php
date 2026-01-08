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
        Schema::table('pink_ride_settings', function (Blueprint $table) {
            $table->string('verfiy_phone_passenger')->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pink_ride_settings', function (Blueprint $table) {
            $table->dropColumn(['verfiy_phone_passenger']);
        });
    }
};
