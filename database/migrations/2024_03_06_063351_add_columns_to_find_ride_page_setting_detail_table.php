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
            $table->text('card_section_at_label')->after('search_section_recent_searches')->nullable();
            $table->text('card_section_seats_left')->after('card_section_at_label')->nullable();
            $table->text('card_section_per_seat')->after('card_section_seats_left')->nullable();
            $table->text('card_section_booked')->after('card_section_per_seat')->nullable();
            $table->text('card_section_seats')->after('card_section_booked')->nullable();
            $table->text('card_section_booking_fee')->after('card_section_seats')->nullable();
            $table->text('card_section_seats_fee')->after('card_section_booking_fee')->nullable();
            $table->text('card_section_amount')->after('card_section_seats_fee')->nullable();
            $table->text('card_section_driver')->after('card_section_amount')->nullable();
            $table->text('card_section_age')->after('card_section_driver')->nullable();
            $table->text('card_section_driven')->after('card_section_age')->nullable();
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
            $table->dropColumn(['card_section_at_label','card_section_seats_left','card_section_per_seat','card_section_booked','card_section_seats',
                'card_section_booking_fee','card_section_seats_fee','card_section_amount','card_section_driver','card_section_age','card_section_driven']);
        });
    }
};
