<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('px_bookings', function (Blueprint $table) {
            $table->string('removed_permanently')->nullable()->after('booked_at');
            $table->integer('block_days')->nullable()->after('removed_permanently');
            $table->dateTime('block_date_time')->nullable()->after('block_days');

            $table->index(['passenger_id', 'removed_permanently', 'block_date_time'], 'px_bookings_passenger_block_idx');
        });
    }

    public function down(): void
    {
        Schema::table('px_bookings', function (Blueprint $table) {
            $table->dropIndex('px_bookings_passenger_block_idx');
            $table->dropColumn(['removed_permanently', 'block_days', 'block_date_time']);
        });
    }
};
