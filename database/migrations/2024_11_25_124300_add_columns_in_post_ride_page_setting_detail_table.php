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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('booking_option1_tooltip')->after('booking_option1')->nullable();
            $table->text('booking_option2_tooltip')->after('booking_option2')->nullable();
        });

        DB::table('post_ride_page_setting_detail')->update([
            'booking_option1_tooltip' => 'Your booking will be confirmed immediately without any additional approval steps.',
            'booking_option2_tooltip' => 'Your booking will be reviewed and approved manually before confirmation.',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['booking_option2_tooltip', 'booking_option1_tooltip']);
        });
    }
};
