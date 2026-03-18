<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->text('cancel_ride_confirm_decision_title')->nullable()->after('cancel_booking_confirm_firm_message');
            $table->text('cancel_ride_confirm_ok_btn_text')->nullable()->after('cancel_ride_confirm_decision_title');
        });
    }

    public function down()
    {
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['cancel_ride_confirm_decision_title', 'cancel_ride_confirm_ok_btn_text']);
        });
    }
};

