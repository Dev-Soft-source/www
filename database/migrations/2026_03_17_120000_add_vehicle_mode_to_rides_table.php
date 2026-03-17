<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->string('vehicle_mode')->default('skip')->after('seats');
        });

        DB::table('rides')->update([
            'vehicle_mode' => DB::raw("
                CASE
                    WHEN added_vehicle = '1' THEN 'existing'
                    WHEN add_vehicle = '1' THEN 'add_new'
                    ELSE 'skip'
                END
            "),
        ]);
    }

    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn('vehicle_mode');
        });
    }
};
