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
        Schema::create('find_ride_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('find_ride_page_setting_id')
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
            $table->string('name')->nullable();
            $table->text('meta_keywords', 1000)->nullable();
            $table->text('meta_description', 1000)->nullable();
            $table->string('main_heading')->nullable();
            $table->string('search_section_from_placeholder')->nullable();
            $table->string('search_section_to_placeholder')->nullable();
            $table->string('search_section_date_placeholder')->nullable();
            $table->string('search_section_required_error')->nullable();
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
            $table->string('payment_methods_option2')->nullable();
            $table->string('payment_methods_option3')->nullable();
            $table->string('payment_methods_option4')->nullable();
            $table->string('filter4_vehicle_heading')->nullable();
            $table->string('vehicle_type_label')->nullable();
            $table->string('vehicle_type_placeholder')->nullable();
            $table->string('ride_features_option1')->nullable();
            $table->string('ride_features_option2')->nullable();
            $table->string('ride_features_option3')->nullable();
            $table->string('ride_features_option4')->nullable();
            $table->string('ride_features_option5')->nullable();
            $table->string('ride_features_option6')->nullable();
            $table->string('ride_features_option7')->nullable();
            $table->string('ride_features_option8')->nullable();
            $table->string('ride_features_option9')->nullable();
            $table->string('ride_features_option10')->nullable();
            $table->string('ride_features_option11')->nullable();
            $table->string('ride_features_option12')->nullable();
            $table->string('ride_features_option13')->nullable();
            $table->string('ride_features_option14')->nullable();
            $table->string('luggage_label')->nullable();
            $table->string('luggage_placeholder')->nullable();
            $table->string('smoking_label')->nullable();
            $table->string('smoking_option1')->nullable();
            $table->string('smoking_option2')->nullable();
            $table->string('smoking_option3')->nullable();
            $table->string('pets_allowed_label')->nullable();
            $table->string('pets_allowed_option1')->nullable();
            $table->string('pets_allowed_option2')->nullable();
            $table->string('pets_allowed_option3')->nullable();
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
        Schema::dropIfExists('find_ride_page_setting_detail');
        Schema::dropIfExists('find_ride_page_setting');
    }
};
