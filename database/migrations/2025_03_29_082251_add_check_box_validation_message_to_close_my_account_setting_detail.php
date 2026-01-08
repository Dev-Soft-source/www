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
        Schema::table('close_my_account_setting_detail', function (Blueprint $table) {
            $table->text('check_box_validation_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('close_my_account_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['check_box_validation_message']);
        });
    }
};
