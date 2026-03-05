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
        Schema::table('px_ride_stops', function (Blueprint $table) {
            $table->text('pickup_dropoff_location')->nullable()->after('lng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('px_ride_stops', function (Blueprint $table) {
            $table->dropColumn('pickup_dropoff_location');
        });
    }
};
