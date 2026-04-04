<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('my_ride_page_heading_label')->nullable()->after('ride_main_heading');
            $table->text('my_trip_page_heading_label')->nullable()->after('my_ride_page_heading_label');
        });
    }

    public function down(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['my_ride_page_heading_label', 'my_trip_page_heading_label']);
        });
    }
};
