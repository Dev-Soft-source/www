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
        Schema::create('post_ride_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_ride_page_setting_id')
                ->constrained()
                ->on('post_ride_page_setting')
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
            $table->string('main_heading')->nullable();
            $table->string('from_label')->nullable();
            $table->string('from_placeholder')->nullable();
            $table->string('to_label')->nullable();
            $table->string('to_placeholder')->nullable();
            $table->string('pick_up_label')->nullable();
            $table->string('pick_up_placeholder')->nullable();
            $table->string('drop_off_label')->nullable();
            $table->string('drop_off_placeholder')->nullable();
            $table->string('date_time_label')->nullable();
            $table->string('at_label')->nullable();
            $table->string('recurring_label')->nullable();
            $table->string('meeting_drop_off_description_label')->nullable();
            $table->text('meeting_drop_off_description_placeholder')->nullable();
            $table->string('seats_label')->nullable();
            $table->string('seats_middle_label')->nullable();
            $table->string('seats_back_label')->nullable();
            $table->string('vehicle_label')->nullable();
            $table->string('skip_label')->nullable();
            $table->string('model_label')->nullable();
            $table->string('type_label')->nullable();
            $table->string('year_label')->nullable();
            $table->string('color_label')->nullable();
            $table->string('liscense_label')->nullable();
            $table->string('electric_car_label')->nullable();
            $table->string('hybrid_car_label')->nullable();
            $table->string('preferences_label')->nullable();
            $table->string('smoking_label')->nullable();
            $table->string('smoking_option1')->nullable();
            $table->string('smoking_option2')->nullable();
            $table->string('animals_label')->nullable();
            $table->text('animals_option1')->nullable();
            $table->text('animals_option2')->nullable();
            $table->text('animals_option3')->nullable();
            $table->text('features_label')->nullable();
            $table->text('features_option1')->nullable();
            $table->text('features_option2')->nullable();
            $table->text('features_option3')->nullable();
            $table->text('features_option4')->nullable();
            $table->text('features_option5')->nullable();
            $table->text('features_option6')->nullable();
            $table->text('features_option7')->nullable();
            $table->text('features_option8')->nullable();
            $table->text('features_option9')->nullable();
            $table->text('features_option10')->nullable();
            $table->text('features_option11')->nullable();
            $table->text('features_option12')->nullable();
            $table->text('features_option13')->nullable();
            $table->string('booking_label')->nullable();
            $table->string('booking_option1')->nullable();
            $table->string('booking_option2')->nullable();
            $table->string('max_back_seats_label')->nullable();
            $table->string('luggage_label')->nullable();
            $table->string('luggage_option1')->nullable();
            $table->string('luggage_option2')->nullable();
            $table->string('luggage_option3')->nullable();
            $table->string('luggage_option4')->nullable();
            $table->string('luggage_option5')->nullable();
            $table->text('luggage_checkbox_label1')->nullable();
            $table->text('luggage_checkbox_label2')->nullable();
            $table->string('price_per_seat_label')->nullable();
            $table->string('payment_methods_label')->nullable();
            $table->string('payment_methods_option1')->nullable();
            $table->string('payment_methods_option2')->nullable();
            $table->string('payment_methods_option3')->nullable();
            $table->string('anything_to_add_label')->nullable();
            $table->text('anything_to_add_placeholder')->nullable();
            $table->string('disclaimers_label')->nullable();
            $table->longText('disclaimers_description')->nullable();
            $table->text('agree_terms_label')->nullable();
            $table->string('submit_button_label')->nullable();
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
        Schema::dropIfExists('post_ride_page_setting_detail');
        Schema::dropIfExists('post_ride_page_setting');
    }
};
