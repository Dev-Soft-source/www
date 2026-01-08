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
        Schema::table('folk_ride_settings', function (Blueprint $table) {
            $table->string('verfiy_phone')->nullable();
            $table->string('verify_email')->nullable();
            $table->string('driver_license')->nullable();
        });

        // Check if the table exists
        if (Schema::hasTable('folk_ride_settings')) {
            if (DB::table('folk_ride_settings')->count() == 0) {
                // Insert default values
                DB::table('folk_ride_settings')->insert([
                    'average_rating' => '4.5',
                    'driver_age' => '35',
                    'verfiy_phone' => '1',
                    'verify_email' => '1',
                    'driver_license' => '1',
                ]);
            } else {
                // Update existing records where fields are null
                DB::table('folk_ride_settings')->whereNull('average_rating')->update(['average_rating' => '4.5']);
                DB::table('folk_ride_settings')->whereNull('driver_age')->update(['driver_age' => '35']);
                DB::table('folk_ride_settings')->whereNull('verfiy_phone')->update(['verfiy_phone' => '1']);
                DB::table('folk_ride_settings')->whereNull('verify_email')->update(['verify_email' => '1']);
                DB::table('folk_ride_settings')->whereNull('driver_license')->update(['driver_license' => '1']);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('folk_ride_settings', function (Blueprint $table) {
            $table->dropColumn(['driver_license', 'verify_email', 'verfiy_phone']);
        });
    }
};
