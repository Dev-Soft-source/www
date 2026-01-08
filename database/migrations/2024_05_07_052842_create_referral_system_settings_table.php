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
        Schema::create('referral_system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('p_2_p_booking_credit')->nullable();
            $table->string('p_2_s_booking_credit')->nullable();
            $table->string('p_2_d_booking_credit')->nullable();
            $table->string('d_2_p_reward_point')->nullable();
            $table->string('d_2_s_reward_point')->nullable();
            $table->string('d_2_d_rewad_point')->nullable();
            $table->string('s_2_p_reward_point')->nullable();
            $table->string('s_2_s_reward_point')->nullable();
            $table->string('s_2_d_reward_point')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_system_settings');
    }
};
