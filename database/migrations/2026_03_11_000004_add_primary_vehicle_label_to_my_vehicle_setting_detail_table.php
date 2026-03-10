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
        Schema::table('my_vehicle_setting_detail', function (Blueprint $table) {
            $table->text('primary_vehicle_label')->nullable()->after('set_primary_vehicle_label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_vehicle_setting_detail', function (Blueprint $table) {
            $table->dropColumn('primary_vehicle_label');
        });
    }
};
