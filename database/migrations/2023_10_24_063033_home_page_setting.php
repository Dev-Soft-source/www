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
        Schema::create('home_page_setting', function (Blueprint $table) {
            $table->id();
            $table->string('facebook_image_path')->nullable();
            $table->timestamps();
        });
        Schema::create('home_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_page_setting_id')
                ->constrained()
                ->on('home_page_setting')
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
            $table->string('slider_heading')->nullable();
            $table->string('slider_from_placeholder')->nullable();
            $table->string('slider_to_placeholder')->nullable();
            $table->string('slider_date_placeholder')->nullable();
            $table->string('slider_required_error')->nullable();
            $table->string('section1_main_heading')->nullable();
            $table->string('section1_pink_rides_label')->nullable();
            $table->longText('section1_pink_rides_description')->nullable();
            $table->string('section1_folks_rides_label')->nullable();
            $table->longText('section1_folks_rides_description')->nullable();
            $table->string('section1_customize_label')->nullable();
            $table->longText('section1_customize_description')->nullable();
            $table->string('section2_main_heading')->nullable();
            $table->string('section2_profile_verification_label')->nullable();
            $table->longText('section2_profile_verification_description')->nullable();
            $table->string('section2_policies_label')->nullable();
            $table->longText('section2_policies_description')->nullable();
            $table->string('section2_car_insurance_label')->nullable();
            $table->longText('section2_car_insurance_description')->nullable();
            $table->string('section2_help_label')->nullable();
            $table->longText('section2_help_description')->nullable();
            $table->string('section3_main_heading')->nullable();
            $table->string('section3_safe_label')->nullable();
            $table->longText('section3_safe_description')->nullable();
            $table->string('section3_affordable_label')->nullable();
            $table->longText('section3_affordable_description')->nullable();
            $table->string('section3_reliable_label')->nullable();
            $table->longText('section3_reliable_description')->nullable();
            $table->string('section4_main_heading')->nullable();
            $table->string('work_section_main_heading')->nullable();
            $table->longText('work_section_main_text')->nullable();
            $table->string('work_section_passenger_label')->nullable();
            $table->longText('work_section_passenger_description')->nullable();
            $table->string('work_section_passenger_point1_label')->nullable();
            $table->longText('work_section_passenger_point1_description')->nullable();
            $table->string('work_section_passenger_point2_label')->nullable();
            $table->longText('work_section_passenger_point2_description')->nullable();
            $table->string('work_section_passenger_point3_label')->nullable();
            $table->longText('work_section_passenger_point3_description')->nullable();
            $table->string('work_section_passenger_point4_label')->nullable();
            $table->longText('work_section_passenger_point4_description')->nullable();
            $table->string('work_section_passenger_point5_label')->nullable();
            $table->longText('work_section_passenger_point5_description')->nullable();
            $table->string('work_section_driver_label')->nullable();
            $table->longText('work_section_driver_description')->nullable();
            $table->string('work_section_driver_point1_label')->nullable();
            $table->longText('work_section_driver_point1_description')->nullable();
            $table->string('work_section_driver_point2_label')->nullable();
            $table->longText('work_section_driver_point2_description')->nullable();
            $table->string('work_section_driver_point3_label')->nullable();
            $table->longText('work_section_driver_point3_description')->nullable();
            $table->string('work_section_driver_point4_label')->nullable();
            $table->longText('work_section_driver_point4_description')->nullable();
            $table->string('work_section_driver_point5_label')->nullable();
            $table->longText('work_section_driver_point5_description')->nullable();
            $table->string('doing_section_main_heading')->nullable();
            $table->text('doing_section_main_text')->nullable();
            $table->string('doing_section_label1')->nullable();
            $table->string('doing_section_label2')->nullable();
            $table->string('reasons_section_main_heading')->nullable();
            $table->text('reasons_section_main_text')->nullable();
            $table->string('reasons_section_members_label')->nullable();
            $table->text('reasons_section_members_description')->nullable();
            $table->string('reasons_section_driver_label')->nullable();
            $table->text('reasons_section_driver_description')->nullable();
            $table->string('reasons_section_quality_label')->nullable();
            $table->text('reasons_section_quality_description')->nullable();
            $table->string('reasons_section_policy_label')->nullable();
            $table->text('reasons_section_policy_description')->nullable();
            $table->string('reasons_section_students_label')->nullable();
            $table->text('reasons_section_students_description')->nullable();
            $table->string('reasons_section_safety_label')->nullable();
            $table->text('reasons_section_safety_description')->nullable();
            $table->string('reasons_section_price_label')->nullable();
            $table->text('reasons_section_price_description')->nullable();
            $table->string('reasons_section_use_label')->nullable();
            $table->text('reasons_section_use_description')->nullable();
            $table->string('movement_section_heading')->nullable();
            $table->text('movement_section_text')->nullable();
            $table->string('members_section_heading')->nullable();
            $table->text('members_section_text')->nullable();
            $table->string('news_section_heading')->nullable();
            $table->string('use_section_heading')->nullable();
            $table->text('use_section_text')->nullable();
            $table->string('use_section_point1_label')->nullable();
            $table->text('use_section_point1_description')->nullable();
            $table->string('use_section_point2_label')->nullable();
            $table->text('use_section_point2_description')->nullable();
            $table->string('use_section_point3_label')->nullable();
            $table->text('use_section_point3_description')->nullable();
            $table->string('use_section_point4_label')->nullable();
            $table->text('use_section_point4_description')->nullable();
            $table->string('reliability_section_heading')->nullable();
            $table->text('reliability_section_text')->nullable();
            $table->string('reliability_section_passengers_label')->nullable();
            $table->longText('reliability_section_passengers_description')->nullable();
            $table->string('reliability_section_drivers_label')->nullable();
            $table->longText('reliability_section_drivers_description')->nullable();
            $table->string('reliability_section_button_label1')->nullable();
            $table->string('reliability_section_button_label2')->nullable();
            $table->string('payment_section_heading')->nullable();
            $table->text('payment_section_text')->nullable();
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
        Schema::dropIfExists('home_page_setting_detail');
        Schema::dropIfExists('home_page_setting');
    }
};
