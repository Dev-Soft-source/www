<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'from_stop_id')) {
                $table->foreignId('from_stop_id')
                    ->nullable()
                    ->after('ride_detail_id')
                    ->constrained('ride_stops')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('bookings', 'to_stop_id')) {
                $table->foreignId('to_stop_id')
                    ->nullable()
                    ->after('from_stop_id')
                    ->constrained('ride_stops')
                    ->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'from_stop_id')) {
                $table->dropForeign(['from_stop_id']);
                $table->dropColumn('from_stop_id');
            }

            if (Schema::hasColumn('bookings', 'to_stop_id')) {
                $table->dropForeign(['to_stop_id']);
                $table->dropColumn('to_stop_id');
            }
        });
    }
};
