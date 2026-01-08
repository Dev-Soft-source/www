<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('pink_ride_settings', function (Blueprint $table) {
            $table->id();
            $table->string('female')->nullable();
            $table->string('verfiy_phone')->nullable();
            $table->string('verify_email')->nullable();
            $table->string('driver_license')->nullable();
        });

        // Check if the table exists
        if (Schema::hasTable('pink_ride_settings')) {
            DB::table('pink_ride_settings')->insert([
                'female' => '1',
                'verfiy_phone' => '1',
                'verify_email' => '1',
                'driver_license' => '1',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pink_ride_settings');
    }
};
