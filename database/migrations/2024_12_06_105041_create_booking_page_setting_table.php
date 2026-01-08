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
        Schema::create('booking_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('booking_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_page_setting_id')
                ->constrained()
                ->on('booking_page_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->text('meta_keywords', 1000)->nullable();
            $table->text('meta_description', 1000)->nullable();
            $table->text('main_heading')->nullable();
            $table->text('seats_available_label')->nullable();
            $table->text('cancellation_policy_label')->nullable();
            $table->text('pricing_label')->nullable();
            $table->text('seat_label')->nullable();
            $table->text('booking_fee_label')->nullable();
            $table->text('total_label')->nullable();
            $table->text('message_to_driver_label')->nullable();
            $table->text('message_driver_placeholder')->nullable();
            $table->text('book_seat_button_label')->nullable();
            $table->text('like_to_pay_label')->nullable();
            $table->text('credit_card_label')->nullable();
            $table->text('select_card_label')->nullable();
            $table->text('add_card_label')->nullable();
            $table->text('pay_label')->nullable();
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
        Schema::dropIfExists('booking_page_setting_detail');
        Schema::dropIfExists('booking_page_setting');
    }
};
