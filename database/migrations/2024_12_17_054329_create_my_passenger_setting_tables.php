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
        Schema::create('my_passenger_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('my_passenger_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('my_passenger_setting_id')
                ->constrained()
                ->on('find_ride_page_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('remove_ride_btn_label')->nullable();
            $table->text('chat_passenger_btn_label')->nullable();
            $table->text('total_amount_label')->nullable();
            $table->text('my_fare_label')->nullable();
            $table->text('booking_fee_label')->nullable();
            $table->text('seat_booked_label')->nullable();
            $table->text('review_profile_label')->nullable();
            $table->text('age')->nullable();
            $table->text('gender')->nullable();
            $table->text('main_heading')->nullable();
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
        Schema::dropIfExists('my_passenger_setting');
        Schema::dropIfExists('my_passenger_setting_detail');
    }
};
