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
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('booking_disclaimer_on_time')->nullable();
            $table->text('booking_disclaimer_pink_ride')->nullable();
            $table->text('booking_disclaimer_extra_care_ride')->nullable();
            $table->text('booking_disclaimer_firm')->nullable();
            $table->text('booking_term_agree_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['booking_disclaimer_on_time', 'booking_disclaimer_pink_ride', 'booking_disclaimer_extra_care_ride', 'booking_disclaimer_firm', 'booking_term_agree_text']);
        });
    }
};
