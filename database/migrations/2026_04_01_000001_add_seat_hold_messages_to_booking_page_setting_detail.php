<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('seat_hold_alert_message')->nullable()->after('seats_available_info_text');
            $table->text('seat_hold_message')->nullable()->after('seat_hold_alert_message');
        });
    }

    public function down(): void
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['seat_hold_alert_message', 'seat_hold_message']);
        });
    }
};
