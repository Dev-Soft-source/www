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
        Schema::table('password_setting_detail', function (Blueprint $table) {
            $table->string('current_password_error')->nullable();
            $table->string('new_password_error')->nullable();
            $table->string('confirm_new_password_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['confirm_new_password_error', 'new_password_error', 'current_password_error']);
        });
    }
};
