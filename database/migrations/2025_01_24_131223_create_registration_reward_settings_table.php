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
        Schema::create('registration_reward_settings', function (Blueprint $table) {
            $table->id();
            $table->string('passenger_credit_booking')->nullable();
            $table->string('driver_reward_point')->nullable();
            $table->string('student_reward_point')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_reward_settings');
    }
};
