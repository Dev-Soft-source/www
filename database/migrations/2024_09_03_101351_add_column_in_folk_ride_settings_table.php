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
        Schema::table('folk_ride_settings', function (Blueprint $table) {
            $table->string('profile_complete')->nullable()->default('1');
        });

        Schema::table('pink_ride_settings', function (Blueprint $table) {
            $table->string('profile_complete')->nullable()->default('1');
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
            $table->dropColumn(['profile_complete']);
        });

        Schema::table('folk_ride_settings', function (Blueprint $table) {
            $table->dropColumn(['profile_complete']);
        });
    }
};
