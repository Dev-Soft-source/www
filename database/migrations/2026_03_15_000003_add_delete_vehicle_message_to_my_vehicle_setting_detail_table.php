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
            $table->string('delete_vehicle_message')->nullable()->after('delete_photo_message');
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
            $table->dropColumn('delete_vehicle_message');
        });
    }
};
