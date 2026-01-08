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
            $table->text('card_section_from_label')->after('search_section_recent_searches')->nullable();
            $table->text('card_section_to_label')->after('card_section_from_label')->nullable();
            $table->text('card_section_passengers')->after('card_section_driven')->nullable();
            $table->text('card_section_review')->after('card_section_passengers')->nullable();
            $table->text('card_section_completed')->after('card_section_review')->nullable();
            $table->text('trips_card_section_seat_booked')->after('card_section_completed')->nullable();
            $table->text('trips_card_section_seat_available')->after('trips_card_section_seat_booked')->nullable();
            $table->text('trips_card_section_review_driver')->after('trips_card_section_seat_available')->nullable();
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
            $table->dropColumn(['trips_card_section_review_driver', 'trips_card_section_seat_available', 'trips_card_section_seat_booked',
                'card_section_completed', 'card_section_review', 'card_section_passengers', 'card_section_to_label', 'card_section_from_label',
            ]);
        });
    }
};
