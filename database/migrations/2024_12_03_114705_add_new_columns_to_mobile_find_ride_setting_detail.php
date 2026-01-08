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
        Schema::table('mobile_find_ride_setting_detail', function (Blueprint $table) {
            $table->text('search_section_keyword_placeholder')->after('search_section_keyword_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_find_ride_setting_detail', function (Blueprint $table) {
            //
        });
    }
};
