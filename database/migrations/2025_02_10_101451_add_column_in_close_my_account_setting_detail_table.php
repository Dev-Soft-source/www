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
            $table->string('close_my_account_checkbox_error')->nullable();
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
            $table->dropColumn(['close_my_account_checkbox_error']);
        });
    }
};
