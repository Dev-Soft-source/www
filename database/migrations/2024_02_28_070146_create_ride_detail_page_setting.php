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
        Schema::create('ride_detail_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_detail_page_id')
                ->constrained()
                ->on('ride_detail_page_setting')
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
            $table->string('from_label')->nullable();
            $table->string('to_label')->nullable();
            $table->string('at_label')->nullable();
            $table->text('co_passenger_label')->nullable();
            $table->text('payment_method_label')->nullable();
            $table->string('luggage_label')->nullable();
            $table->text('seats_left_label')->nullable();
            $table->text('per_seat_label')->nullable();
            $table->text('ride_features_label')->nullable();
            $table->text('all_seats_booked_label')->nullable();
            $table->text('vehicle_info_label')->nullable();
            $table->text('driver_info_label')->nullable();
            $table->string('driver_label')->nullable();
            $table->string('driver_age_label')->nullable();
            $table->text('driver_chat_heading')->nullable();
            $table->text('driver_chat_label')->nullable();
            $table->string('driver_chat_button_label')->nullable();
            $table->text('booking_table_heading')->nullable();
            $table->string('passenger_column_label')->nullable();
            $table->text('seat_booked_column_label')->nullable();
            $table->text('total_cost_column_label')->nullable();
            $table->text('booked_on_column_label')->nullable();
            $table->string('status_column_label')->nullable();
            $table->text('booking_requested_status_label')->nullable();
            $table->text('seat_booked_status_label')->nullable();
            $table->text('booking_denied_status_label')->nullable();
            $table->string('actions_column_label')->nullable();
            $table->string('edit_button_actions_label')->nullable();
            $table->string('review_button_label')->nullable();
            $table->text('i_reviewed_label')->nullable();
            $table->text('driver_note_label')->nullable();
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
        Schema::dropIfExists('ride_detail_page_setting_detail');
        Schema::dropIfExists('ride_detail_page_setting');
    }
};
