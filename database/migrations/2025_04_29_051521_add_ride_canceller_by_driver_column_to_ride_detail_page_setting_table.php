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
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('ride_canceller_by_driver')->nullable();
            $table->text('review_driver_info_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('ride_canceller_by_driver');
            $table->dropColumn('review_driver_info_label');
        });
    }
};
