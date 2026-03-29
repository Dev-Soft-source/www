<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'from_stop_id')) {
                $table->foreignId('from_stop_id')
                    ->nullable()
                    ->after('destination')
                    ->constrained('ride_stops')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('notifications', 'to_stop_id')) {
                $table->foreignId('to_stop_id')
                    ->nullable()
                    ->after('from_stop_id')
                    ->constrained('ride_stops')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'to_stop_id')) {
                $table->dropForeign(['to_stop_id']);
                $table->dropColumn('to_stop_id');
            }

            if (Schema::hasColumn('notifications', 'from_stop_id')) {
                $table->dropForeign(['from_stop_id']);
                $table->dropColumn('from_stop_id');
            }
        });
    }
};
