<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('request_booking_label')->nullable()->after('booking_request_heading');
            $table->text('view_cancellation_tooltip')->nullable()->after('cancellation_policy_tooltip');
        });

        Schema::table('step1_page_setting_detail', function (Blueprint $table) {
            $table->text('alert_age_limit_text')->nullable()->after('dob_error');
        });

        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('pay_and_request_to_book_btn_text')->nullable()->after('book_seat_button_label');
        });

        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('hide_full_ride_text')->nullable()->after('search_section_button_label');
        });

        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('stops_remove_confirm_text')->nullable()->after('delete_stop_text');
            $table->text('stop_suggest_label')->nullable()->after('stops_remove_confirm_text');
            $table->text('distance_suffix')->nullable()->after('stop_suggest_label');
            $table->text('stops_along_the_way_label')->nullable()->after('distance_suffix');
        });
    }

    public function down()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['request_booking_label', 'view_cancellation_tooltip']);
        });

        Schema::table('step1_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('alert_age_limit_text');
        });

        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('pay_and_request_to_book_btn_text');
        });

        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('hide_full_ride_text');
        });

        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'stops_remove_confirm_text',
                'stop_suggest_label',
                'distance_suffix',
                'stops_along_the_way_label',
            ]);
        });
    }
};
