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
        Schema::table('ride_details', function (Blueprint $table) {
            $table->integer('origin_city_id')->nullable()->after('departure');
            $table->integer('destination_city_id')->nullable()->after('destination');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_details', function (Blueprint $table) {
            $table->dropColumn(['origin_city_id', 'destination_city_id']);
        });
    }
};
