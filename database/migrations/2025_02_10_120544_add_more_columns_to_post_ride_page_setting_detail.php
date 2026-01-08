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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('extra_care_tooltip_admin_enable_text')->nullable();
            $table->text('extra_care_tooltip_admin_disable_text')->nullable();
            $table->text('pink_ride_tooltip_admin_enable_text')->nullable();
            $table->text('pink_ride_tooltip_admin_disable_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            //
        });
    }
};
