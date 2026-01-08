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
        Schema::table('reset_password_page_setting_detail', function (Blueprint $table) {
            $table->string('password_error')->nullable();
            $table->string('confirm_password_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reset_password_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['confirm_password_error', 'password_error']);
        });
    }
};
