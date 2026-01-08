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
            $table->text('cancellation_policy_tooltip')->nullable();
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
            $table->dropColumn(['cancellation_policy_tooltip']);
        });
    }
};
