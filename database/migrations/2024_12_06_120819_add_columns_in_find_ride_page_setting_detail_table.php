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
            $table->text('search_result_load_more_btn')->nullable();
            $table->text('search_result_no_more_data_message')->nullable();
            $table->text('search_result_no_found_message')->nullable();
            $table->text('search_result_label')->nullable();
            $table->text('filter_what_label')->nullable();
            $table->text('search_and_above_label')->nullable();
            $table->text('search_filter_all_label')->nullable();
            $table->text('search_filter_select_vehicle_label')->nullable();
            $table->text('card_section_not_live')->nullable();
            $table->text('card_section_booking_request')->nullable();
            $table->text('trips_card_section_reviewed')->nullable();
            $table->text('card_section_no_review')->nullable();
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
            $table->dropColumn(['card_section_no_review', 'trips_card_section_reviewed', 'card_section_booking_request',
                'card_section_not_live', 'search_filter_select_vehicle_label', 'search_filter_all_label', 'search_and_above_label',
                'filter_what_label', 'search_result_label', 'search_result_no_found_message', 'search_result_no_more_data_message',
                'search_result_load_more_btn'
            ]);
        });
    }
};
