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
        Schema::create('my_wallet_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('my_wallet_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_setting_id')
                ->constrained()
                ->on('my_wallet_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('main_heading')->nullable();
            $table->string('card_heading')->nullable();
            $table->string('passenger_heading')->nullable();
            $table->string('driver_heading')->nullable();
            $table->string('balance_heading')->nullable();
            $table->string('passenger_my_ride_heading')->nullable();
            $table->string('passenger_ride_id_label')->nullable();
            $table->string('passenger_my_ride_from_label')->nullable();
            $table->string('passenger_my_ride_to_label')->nullable();
            $table->string('passenger_my_ride_date_label')->nullable();
            $table->string('passenger_my_ride_booking_fee_label')->nullable();
            $table->string('passenger_my_ride_fare_label')->nullable();
            $table->string('passenger_my_ride_total_amount_label')->nullable();
            $table->string('passenger_my_reward_heading')->nullable();
            $table->string('passenger_my_reward_description')->nullable();
            $table->string('passenger_my_reward_points_table_label')->nullable();
            $table->string('passenger_my_reward_reward_table_label')->nullable();
            $table->string('passenger_my_reward_to_label')->nullable();
            $table->string('driver_paid_out_heading')->nullable();
            $table->string('driver_availabe_heading')->nullable();
            $table->string('driver_paid_ride_id_label')->nullable();
            $table->string('driver_paid_from_label')->nullable();
            $table->string('driver_paid_to_label')->nullable();
            $table->string('driver_paid_paid_out_date_label')->nullable();
            $table->string('driver_paid_total_amount_label')->nullable();
            $table->string('driver_available_ride_id_label')->nullable();
            $table->string('driver_available_from_label')->nullable();
            $table->string('driver_available_to_label')->nullable();
            $table->string('driver_available_date_label')->nullable();
            $table->string('driver_available_total_amount_label')->nullable();
            $table->string('driver_pending_heading')->nullable();
            $table->string('driver_pending_data_description')->nullable();
            $table->string('driver_reward_heading')->nullable();
            $table->string('driver_reward_description')->nullable();
            $table->string('driver_reward_points_table_label')->nullable();
            $table->string('driver_reward_reward_table_label')->nullable();
            $table->string('driver_reward_to_label')->nullable();
            $table->string('balance_id_label')->nullable();
            $table->string('balance_amount_label')->nullable();
            $table->string('balance_date_label')->nullable();
            $table->string('balance_buy_more_button_text')->nullable();
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
        Schema::dropIfExists('my_wallet_setting_detail');
        Schema::dropIfExists('my_wallet_setting');
    }
};
