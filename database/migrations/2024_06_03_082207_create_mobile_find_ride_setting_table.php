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
        Schema::create('mobile_find_ride_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('mobile_find_ride_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('find_ride_setting_id')
                ->constrained()
                ->on('mobile_find_ride_setting')
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
            $table->string('search_section_from_label')->nullable();
            $table->string('search_section_from_placeholder')->nullable();
            $table->string('search_section_to_label')->nullable();
            $table->string('search_section_to_placeholder')->nullable();
            $table->string('search_section_keyword_label')->nullable();
            $table->string('search_section_date_placeholder')->nullable();
            $table->string('search_section_button_label')->nullable();
            $table->string('search_section_recent_searches')->nullable();
            $table->text('card_section_at_label')->nullable();
            $table->text('card_section_per_seat')->nullable();
            $table->text('card_section_age')->nullable();
            $table->text('card_section_driven')->nullable();
            $table->text('card_section_review')->nullable();
            $table->string('filter_section_heading')->nullable();
            $table->string('filter1_driver_heading')->nullable();
            $table->string('driver_age_label')->nullable();
            $table->string('driver_age_placeholder')->nullable();
            $table->string('driver_rating_label')->nullable();
            $table->string('driver_rating_placeholder')->nullable();
            $table->string('driver_phone_access_label')->nullable();
            $table->string('driver_know_label')->nullable();
            $table->string('driver_know_placeholder')->nullable();
            $table->string('filter2_passengers_heading')->nullable();
            $table->string('passengers_rating_label')->nullable();
            $table->string('passengers_rating_placeholder')->nullable();
            $table->string('filter3_payment_methods_heading')->nullable();
            $table->string('payment_methods_option1')->nullable();
            $table->string('filter4_vehicle_heading')->nullable();
            $table->string('vehicle_type_placeholder')->nullable();
            $table->string('ride_preferences_label')->nullable();
            $table->string('luggage_label')->nullable();
            $table->string('smoking_label')->nullable();
            $table->string('pets_allowed_label')->nullable();
            $table->string('clear_button_label')->nullable();
            $table->string('apply_button_label')->nullable();
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
        Schema::dropIfExists('mobile_find_ride_setting_detail');
        Schema::dropIfExists('mobile_find_ride_setting');
    }
};
