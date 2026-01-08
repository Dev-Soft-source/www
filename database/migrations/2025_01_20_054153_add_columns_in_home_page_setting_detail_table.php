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
        Schema::table('home_page_setting_detail', function (Blueprint $table) {
            $table->text('section2_profile_verification_image')->after('section2_profile_verification_label')->nullable();
            $table->text('section2_policies_image')->after('section2_policies_label')->nullable();
            $table->text('section2_car_insurance_image')->after('section2_car_insurance_label')->nullable();
            $table->text('section2_help_image')->after('section2_help_label')->nullable();
            $table->text('section3_safe_image')->after('section3_safe_label')->nullable();
            $table->text('section3_affordable_image')->after('section3_affordable_label')->nullable();
            $table->text('section3_reliable_image')->after('section3_reliable_label')->nullable();
            $table->text('work_section_passenger_point1_image')->after('work_section_passenger_point1_label')->nullable();
            $table->text('work_section_passenger_point2_image')->after('work_section_passenger_point2_label')->nullable();
            $table->text('work_section_passenger_point3_image')->after('work_section_passenger_point3_label')->nullable();
            $table->text('work_section_passenger_point4_image')->after('work_section_passenger_point4_label')->nullable();
            $table->text('work_section_passenger_point5_image')->after('work_section_passenger_point5_label')->nullable();
            $table->text('work_section_driver_point1_image')->after('work_section_driver_point1_label')->nullable();
            $table->text('work_section_driver_point2_image')->after('work_section_driver_point2_label')->nullable();
            $table->text('work_section_driver_point3_image')->after('work_section_driver_point3_label')->nullable();
            $table->text('work_section_driver_point4_image')->after('work_section_driver_point4_label')->nullable();
            $table->text('work_section_driver_point5_image')->after('work_section_driver_point5_label')->nullable();
            $table->text('doing_section_slider_image')->after('doing_section_main_text')->nullable();
            $table->text('reasons_section_members_image')->after('reasons_section_members_label')->nullable();
            $table->text('reasons_section_driver_image')->after('reasons_section_driver_label')->nullable();
            $table->text('reasons_section_quality_image')->after('reasons_section_quality_label')->nullable();
            $table->text('reasons_section_policy_image')->after('reasons_section_policy_label')->nullable();
            $table->text('reasons_section_students_image')->after('reasons_section_students_label')->nullable();
            $table->text('reasons_section_safety_image')->after('reasons_section_safety_label')->nullable();
            $table->text('reasons_section_price_image')->after('reasons_section_price_label')->nullable();
            $table->text('reasons_section_use_image')->after('reasons_section_use_label')->nullable();
            $table->text('reasons_section_reliable_image')->after('reasons_section_reliable_label')->nullable();
            $table->text('reasons_section_responsible_image')->after('reasons_section_responsible_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('home_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['section2_profile_verification_image', 'section2_policies_image', 'section2_car_insurance_image',
                'section2_help_image', 'section3_safe_image', 'section3_affordable_image', 'section3_reliable_image', 'work_section_passenger_point1_image',
                'work_section_passenger_point2_image', 'work_section_passenger_point3_image', 'work_section_passenger_point4_image',
                'work_section_passenger_point5_image', 'work_section_driver_point1_image', 'work_section_driver_point2_image', 'work_section_driver_point3_image',
                'work_section_driver_point4_image', 'work_section_driver_point5_image', 'doing_section_slider_image', 'reasons_section_members_image',
                'reasons_section_driver_image', 'reasons_section_quality_image', 'reasons_section_policy_image', 'reasons_section_students_image',
                'reasons_section_safety_image', 'reasons_section_price_image', 'reasons_section_use_image', 'reasons_section_reliable_image', 'reasons_section_responsible_image'
            ]);
        });
    }
};
