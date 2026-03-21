<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['pickup_label', 'dropoff_label']);
        });

        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('depends_on_other_stops_tooltip')->nullable()->after('dropoff_at_label');
            $table->text('departure_time_approximate_tooltip')->nullable()->after('depends_on_other_stops_tooltip');
            $table->text('stops_along_the_way_label')->nullable()->after('departure_time_approximate_tooltip');
        });
    }

    public function down(): void
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['depends_on_other_stops_tooltip', 'departure_time_approximate_tooltip', 'stops_along_the_way_label']);
        });

        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('pickup_label')->nullable()->after('pickup_dropoff_info_heading');
            $table->text('dropoff_label')->nullable()->after('pickup_at_label');
        });
    }
};
