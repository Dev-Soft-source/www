<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('stop_along_the_way_label')->nullable();
            $table->text('add_stop_btn_label')->nullable();
            $table->text('stop_placeholder')->nullable();
            $table->text('pickup_off_placeholder')->nullable();
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'stop_along_the_way_label',
                'add_stop_btn_label',
                'stop_placeholder',
                'pickup_off_placeholder',
            ]);
        });
    }
};
