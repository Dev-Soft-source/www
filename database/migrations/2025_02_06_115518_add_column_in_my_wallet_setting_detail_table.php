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
        Schema::table('my_wallet_setting_detail', function (Blueprint $table) {
            $table->text('passenger_my_reward_description1')->nullable();
            $table->text('driver_my_reward_description1')->nullable();
            $table->text('claim_my_reward_button_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_wallet_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['passenger_my_reward_description1', 'driver_my_reward_description1', 'claim_my_reward_button_text']);
        });
    }
};
