<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('pickup_at_label')->nullable()->after('card_section_at_label');
            $table->text('dropoff_at_label')->nullable()->after('pickup_at_label');
            $table->text('total_seats_label')->nullable()->after('card_section_per_seat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['pickup_at_label', 'dropoff_at_label', 'total_seats_label']);
        });
    }
};
