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
        Schema::table('select_location_setting_detail', function (Blueprint $table) {
            $table->string('select_state_first_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('select_location_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['select_state_first_label']);
        });
    }
};
