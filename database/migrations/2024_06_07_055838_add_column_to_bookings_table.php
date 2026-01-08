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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['free_ride', 'time_limit', 'reminder']);
            $table->string('secured_cash')->after('booking_credit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('free_ride')->default('0');
            $table->string('time_limit')->default('0');
            $table->string('reminder')->default('0');
            $table->dropColumn(['secured_cash']);
        });
    }
};
