<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['book_seats_btn_label', 'driver_age_label']);
        });

        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('seat_available_label')->nullable()->after('total_seats_label');
        });
    }

    public function down(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('seat_available_label');
        });

        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('book_seats_btn_label')->nullable()->after('book_seat_btn_label');
            $table->string('driver_age_label')->nullable()->after('passengers_driven_label');
        });
    }
};
