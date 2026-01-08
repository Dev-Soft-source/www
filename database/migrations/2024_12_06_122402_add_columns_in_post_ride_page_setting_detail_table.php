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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('cancelled_ride_no_found_message')->nullable();
            $table->text('upcoming_label')->nullable();
            $table->text('completed_label')->nullable();
            $table->text('cancelled_label')->nullable();
            $table->text('upcoming_ride_no_found_message')->nullable();
            $table->text('completed_ride_no_found_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['completed_ride_no_found_message', 'upcoming_ride_no_found_message',
                'cancelled_label', 'completed_label', 'upcoming_label', 'cancelled_ride_no_found_message'
            ]);
        });
    }
};
