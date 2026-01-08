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
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->text('login_before_booking_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['login_before_booking_message']);
        });
    }
};
