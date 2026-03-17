<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ride_stops', function (Blueprint $table) {
            if (!Schema::hasColumn('ride_stops', 'departure_at')) {
                $table->timestamp('departure_at')->nullable()->after('lng');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_stops', function (Blueprint $table) {
            if (Schema::hasColumn('ride_stops', 'departure_at')) {
                $table->dropColumn('departure_at');
            }
        });
    }
};
