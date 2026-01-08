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
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['driver_license_label', 'driver_license_sub_label']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->string('driver_license_label')->nullable();
            $table->string('driver_license_sub_label')->nullable();
        });
    }
};
