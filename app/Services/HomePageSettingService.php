<?php

namespace App\Services;

use App\Models\HomePageSettingDetail;

class HomePageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_keywords.meta_keywords_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_keywords.meta_keywords_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_description.meta_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_description.meta_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['slider_heading.slider_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['slider_heading.slider_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['slider_required_error.slider_required_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['slider_required_error.slider_required_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['slider_image.slider_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['slider_image.slider_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['from_field_icon.from_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['from_field_icon.from_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['swap_field_icon.swap_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['swap_field_icon.swap_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['to_field_icon.to_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['to_field_icon.to_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['date_field_icon.date_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['date_field_icon.date_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_field_icon.search_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_field_icon.search_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_main_heading.section1_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_main_heading.section1_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_pink_rides_label.section1_pink_rides_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_pink_rides_label.section1_pink_rides_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_pink_rides_image.section1_pink_rides_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_pink_rides_image.section1_pink_rides_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_pink_rides_description.section1_pink_rides_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_pink_rides_description.section1_pink_rides_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_folks_rides_label.section1_folks_rides_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_folks_rides_label.section1_folks_rides_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_folks_rides_image.section1_folks_rides_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_folks_rides_image.section1_folks_rides_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_folks_rides_description.section1_folks_rides_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_folks_rides_description.section1_folks_rides_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_customize_label.section1_customize_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_customize_label.section1_customize_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_customize_image.section1_customize_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_customize_image.section1_customize_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section1_customize_description.section1_customize_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section1_customize_description.section1_customize_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_main_heading.section2_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_main_heading.section2_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_profile_verification_label.section2_profile_verification_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_profile_verification_label.section2_profile_verification_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_profile_verification_image.section2_profile_verification_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_profile_verification_image.section2_profile_verification_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_profile_verification_description.section2_profile_verification_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_profile_verification_description.section2_profile_verification_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_policies_label.section2_policies_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_policies_label.section2_policies_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_policies_image.section2_policies_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_policies_image.section2_policies_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_policies_description.section2_policies_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_policies_description.section2_policies_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_car_insurance_label.section2_car_insurance_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_car_insurance_label.section2_car_insurance_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_car_insurance_image.section2_car_insurance_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_car_insurance_image.section2_car_insurance_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_car_insurance_description.section2_car_insurance_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_car_insurance_description.section2_car_insurance_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_help_label.section2_help_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_help_label.section2_help_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_help_image.section2_help_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_help_image.section2_help_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section2_help_description.section2_help_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section2_help_description.section2_help_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_main_heading.section3_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_main_heading.section3_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_safe_label.section3_safe_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_safe_label.section3_safe_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_safe_image.section3_safe_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_safe_image.section3_safe_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_safe_description.section3_safe_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_safe_description.section3_safe_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_affordable_label.section3_affordable_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_affordable_label.section3_affordable_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_affordable_image.section3_affordable_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_affordable_image.section3_affordable_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_affordable_description.section3_affordable_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_affordable_description.section3_affordable_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_reliable_label.section3_reliable_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_reliable_label.section3_reliable_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_reliable_image.section3_reliable_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_reliable_image.section3_reliable_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section3_reliable_description.section3_reliable_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section3_reliable_description.section3_reliable_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['section4_main_heading.section4_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['section4_main_heading.section4_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_main_heading.work_section_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_main_heading.work_section_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_main_text.work_section_main_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_main_text.work_section_main_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_label.work_section_passenger_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_label.work_section_passenger_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_description.work_section_passenger_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_description.work_section_passenger_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point1_label.work_section_passenger_point1_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point1_label.work_section_passenger_point1_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point1_image.work_section_passenger_point1_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point1_image.work_section_passenger_point1_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point1_description.work_section_passenger_point1_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point1_description.work_section_passenger_point1_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point2_label.work_section_passenger_point2_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point2_label.work_section_passenger_point2_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point2_image.work_section_passenger_point2_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point2_image.work_section_passenger_point2_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point2_description.work_section_passenger_point2_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point2_description.work_section_passenger_point2_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point3_label.work_section_passenger_point3_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point3_label.work_section_passenger_point3_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point3_image.work_section_passenger_point3_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point3_image.work_section_passenger_point3_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point3_description.work_section_passenger_point3_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point3_description.work_section_passenger_point3_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point4_label.work_section_passenger_point4_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point4_label.work_section_passenger_point4_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point4_image.work_section_passenger_point4_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point4_image.work_section_passenger_point4_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point4_description.work_section_passenger_point4_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point4_description.work_section_passenger_point4_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point5_label.work_section_passenger_point5_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point5_label.work_section_passenger_point5_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point5_image.work_section_passenger_point5_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point5_image.work_section_passenger_point5_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_passenger_point5_description.work_section_passenger_point5_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_passenger_point5_description.work_section_passenger_point5_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_label.work_section_driver_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_label.work_section_driver_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_description.work_section_driver_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_description.work_section_driver_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point1_label.work_section_driver_point1_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point1_label.work_section_driver_point1_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point1_image.work_section_driver_point1_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point1_image.work_section_driver_point1_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point1_description.work_section_driver_point1_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point1_description.work_section_driver_point1_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point2_label.work_section_driver_point2_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point2_label.work_section_driver_point2_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point2_image.work_section_driver_point2_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point2_image.work_section_driver_point2_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point2_description.work_section_driver_point2_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point2_description.work_section_driver_point2_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point3_label.work_section_driver_point3_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point3_label.work_section_driver_point3_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point3_image.work_section_driver_point3_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point3_image.work_section_driver_point3_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point3_description.work_section_driver_point3_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point3_description.work_section_driver_point3_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point4_label.work_section_driver_point4_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point4_label.work_section_driver_point4_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point4_image.work_section_driver_point4_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point4_image.work_section_driver_point4_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point4_description.work_section_driver_point4_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point4_description.work_section_driver_point4_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point5_label.work_section_driver_point5_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point5_label.work_section_driver_point5_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point5_image.work_section_driver_point5_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point5_image.work_section_driver_point5_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['work_section_driver_point5_description.work_section_driver_point5_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['work_section_driver_point5_description.work_section_driver_point5_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['doing_section_main_heading.doing_section_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['doing_section_main_heading.doing_section_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['doing_section_main_text.doing_section_main_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['doing_section_main_text.doing_section_main_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['doing_section_slider_image.doing_section_slider_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['doing_section_slider_image.doing_section_slider_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['doing_section_label1.doing_section_label1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['doing_section_label1.doing_section_label1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['doing_section_label2.doing_section_label2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['doing_section_label2.doing_section_label2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_main_heading.reasons_section_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_main_heading.reasons_section_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_main_text.reasons_section_main_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_main_text.reasons_section_main_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_members_label.reasons_section_members_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_members_label.reasons_section_members_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_members_image.reasons_section_members_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_members_image.reasons_section_members_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_members_description.reasons_section_members_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_members_description.reasons_section_members_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_driver_label.reasons_section_driver_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_driver_label.reasons_section_driver_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_driver_image.reasons_section_driver_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_driver_image.reasons_section_driver_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_driver_description.reasons_section_driver_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_driver_description.reasons_section_driver_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_quality_label.reasons_section_quality_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_quality_label.reasons_section_quality_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_quality_image.reasons_section_quality_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_quality_image.reasons_section_quality_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_quality_description.reasons_section_quality_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_quality_description.reasons_section_quality_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_policy_label.reasons_section_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_policy_label.reasons_section_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_policy_image.reasons_section_policy_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_policy_image.reasons_section_policy_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_policy_description.reasons_section_policy_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_policy_description.reasons_section_policy_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_students_label.reasons_section_students_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_students_label.reasons_section_students_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_students_image.reasons_section_students_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_students_image.reasons_section_students_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_students_description.reasons_section_students_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_students_description.reasons_section_students_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_safety_label.reasons_section_safety_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_safety_label.reasons_section_safety_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_safety_image.reasons_section_safety_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_safety_image.reasons_section_safety_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_safety_description.reasons_section_safety_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_safety_description.reasons_section_safety_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_price_label.reasons_section_price_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_price_label.reasons_section_price_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_price_image.reasons_section_price_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_price_image.reasons_section_price_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_price_description.reasons_section_price_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_price_description.reasons_section_price_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_use_label.reasons_section_use_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_use_label.reasons_section_use_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_use_image.reasons_section_use_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_use_image.reasons_section_use_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_use_description.reasons_section_use_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_use_description.reasons_section_use_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_reliable_label.reasons_section_reliable_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_reliable_label.reasons_section_reliable_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_reliable_image.reasons_section_reliable_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_reliable_image.reasons_section_reliable_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_reliable_description.reasons_section_reliable_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_reliable_description.reasons_section_reliable_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_responsible_label.reasons_section_responsible_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_responsible_label.reasons_section_responsible_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_responsible_image.reasons_section_responsible_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_responsible_image.reasons_section_responsible_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reasons_section_responsible_description.reasons_section_responsible_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reasons_section_responsible_description.reasons_section_responsible_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['movement_section_heading.movement_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['movement_section_heading.movement_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['movement_section_icon.movement_section_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['movement_section_icon.movement_section_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['movement_section_text.movement_section_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['movement_section_text.movement_section_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['members_section_heading.members_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['members_section_heading.members_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['members_section_text.members_section_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['members_section_text.members_section_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['news_section_heading.news_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['news_section_heading.news_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['news_section_icon1.news_section_icon1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['news_section_icon1.news_section_icon1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['news_section_icon2.news_section_icon2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['news_section_icon2.news_section_icon2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_heading.use_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_heading.use_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_text.use_section_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_text.use_section_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point1_label.use_section_point1_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point1_label.use_section_point1_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point1_image.use_section_point1_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point1_image.use_section_point1_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point1_description.use_section_point1_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point1_description.use_section_point1_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point2_label.use_section_point2_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point2_label.use_section_point2_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point2_image.use_section_point2_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point2_image.use_section_point2_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point2_description.use_section_point2_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point2_description.use_section_point2_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point3_label.use_section_point3_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point3_label.use_section_point3_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point3_image.use_section_point3_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point3_image.use_section_point3_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point3_description.use_section_point3_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point3_description.use_section_point3_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point4_label.use_section_point4_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point4_label.use_section_point4_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point4_image.use_section_point4_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point4_image.use_section_point4_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['use_section_point4_description.use_section_point4_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['use_section_point4_description.use_section_point4_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_heading.reliability_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_heading.reliability_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_text.reliability_section_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_text.reliability_section_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_passengers_label.reliability_section_passengers_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_passengers_label.reliability_section_passengers_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_passengers_description.reliability_section_passengers_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_passengers_description.reliability_section_passengers_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_drivers_label.reliability_section_drivers_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_drivers_label.reliability_section_drivers_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_drivers_description.reliability_section_drivers_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_drivers_description.reliability_section_drivers_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_button_label1.reliability_section_button_label1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_button_label1.reliability_section_button_label1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reliability_section_button_label2.reliability_section_button_label2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reliability_section_button_label2.reliability_section_button_label2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_section_heading.payment_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_section_heading.payment_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_section_text.payment_section_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_section_text.payment_section_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_section_icon1.payment_section_icon1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_section_icon1.payment_section_icon1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_section_icon2.payment_section_icon2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_section_icon2.payment_section_icon2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['slider_from_placeholder.slider_from_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['slider_from_placeholder.slider_from_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['slider_to_placeholder.slider_to_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['slider_to_placeholder.slider_to_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
            }
        }
    }

    public function fields($homePageSetting, $language, $request)
    {
        return [
            'home_page_setting_id' => $homePageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'slider_heading' => $this->data($request, $language, 'slider_heading'),
            'slider_from_placeholder' => $this->data($request, $language, 'slider_from_placeholder'),
            'slider_to_placeholder' => $this->data($request, $language, 'slider_to_placeholder'),
            'slider_date_placeholder' => $this->data($request, $language, 'slider_date_placeholder'),
            'slider_required_error' => $this->data($request, $language, 'slider_required_error'),
            'slider_image' => $this->data($request, $language, 'slider_image'),
            'from_field_icon' => $this->data($request, $language, 'from_field_icon'),
            'swap_field_icon' => $this->data($request, $language, 'swap_field_icon'),
            'to_field_icon' => $this->data($request, $language, 'to_field_icon'),
            'date_field_icon' => $this->data($request, $language, 'date_field_icon'),
            'search_field_icon' => $this->data($request, $language, 'search_field_icon'),
            'section1_main_heading' => $this->data($request, $language, 'section1_main_heading'),
            'section1_pink_rides_label' => $this->data($request, $language, 'section1_pink_rides_label'),
            'section1_pink_rides_image' => $this->data($request, $language, 'section1_pink_rides_image'),
            'section1_pink_rides_description' => $this->data($request, $language, 'section1_pink_rides_description'),
            'section1_folks_rides_label' => $this->data($request, $language, 'section1_folks_rides_label'),
            'section1_folks_rides_image' => $this->data($request, $language, 'section1_folks_rides_image'),
            'section1_folks_rides_description' => $this->data($request, $language, 'section1_folks_rides_description'),
            'section1_customize_label' => $this->data($request, $language, 'section1_customize_label'),
            'section1_customize_image' => $this->data($request, $language, 'section1_customize_image'),
            'section1_customize_description' => $this->data($request, $language, 'section1_customize_description'),
            'section2_main_heading' => $this->data($request, $language, 'section2_main_heading'),
            'section2_profile_verification_label' => $this->data($request, $language, 'section2_profile_verification_label'),
            'section2_profile_verification_image' => $this->data($request, $language, 'section2_profile_verification_image'),
            'section2_profile_verification_description' => $this->data($request, $language, 'section2_profile_verification_description'),
            'section2_policies_label' => $this->data($request, $language, 'section2_policies_label'),
            'section2_policies_image' => $this->data($request, $language, 'section2_policies_image'),
            'section2_policies_description' => $this->data($request, $language, 'section2_policies_description'),
            'section2_car_insurance_label' => $this->data($request, $language, 'section2_car_insurance_label'),
            'section2_car_insurance_image' => $this->data($request, $language, 'section2_car_insurance_image'),
            'section2_car_insurance_description' => $this->data($request, $language, 'section2_car_insurance_description'),
            'section2_help_label' => $this->data($request, $language, 'section2_help_label'),
            'section2_help_image' => $this->data($request, $language, 'section2_help_image'),
            'section2_help_description' => $this->data($request, $language, 'section2_help_description'),
            'section3_main_heading' => $this->data($request, $language, 'section3_main_heading'),
            'section3_safe_label' => $this->data($request, $language, 'section3_safe_label'),
            'section3_safe_image' => $this->data($request, $language, 'section3_safe_image'),
            'section3_safe_description' => $this->data($request, $language, 'section3_safe_description'),
            'section3_affordable_label' => $this->data($request, $language, 'section3_affordable_label'),
            'section3_affordable_image' => $this->data($request, $language, 'section3_affordable_image'),
            'section3_affordable_description' => $this->data($request, $language, 'section3_affordable_description'),
            'section3_reliable_label' => $this->data($request, $language, 'section3_reliable_label'),
            'section3_reliable_image' => $this->data($request, $language, 'section3_reliable_image'),
            'section3_reliable_description' => $this->data($request, $language, 'section3_reliable_description'),
            'section4_main_heading' => $this->data($request, $language, 'section4_main_heading'),
            'work_section_main_heading' => $this->data($request, $language, 'work_section_main_heading'),
            'work_section_main_text' => $this->data($request, $language, 'work_section_main_text'),
            'work_section_passenger_label' => $this->data($request, $language, 'work_section_passenger_label'),
            'work_section_passenger_description' => $this->data($request, $language, 'work_section_passenger_description'),
            'work_section_passenger_point1_label' => $this->data($request, $language, 'work_section_passenger_point1_label'),
            'work_section_passenger_point1_image' => $this->data($request, $language, 'work_section_passenger_point1_image'),
            'work_section_passenger_point1_description' => $this->data($request, $language, 'work_section_passenger_point1_description'),
            'work_section_passenger_point2_label' => $this->data($request, $language, 'work_section_passenger_point2_label'),
            'work_section_passenger_point2_image' => $this->data($request, $language, 'work_section_passenger_point2_image'),
            'work_section_passenger_point2_description' => $this->data($request, $language, 'work_section_passenger_point2_description'),
            'work_section_passenger_point3_label' => $this->data($request, $language, 'work_section_passenger_point3_label'),
            'work_section_passenger_point3_image' => $this->data($request, $language, 'work_section_passenger_point3_image'),
            'work_section_passenger_point3_description' => $this->data($request, $language, 'work_section_passenger_point3_description'),
            'work_section_passenger_point4_label' => $this->data($request, $language, 'work_section_passenger_point4_label'),
            'work_section_passenger_point4_image' => $this->data($request, $language, 'work_section_passenger_point4_image'),
            'work_section_passenger_point4_description' => $this->data($request, $language, 'work_section_passenger_point4_description'),
            'work_section_passenger_point5_label' => $this->data($request, $language, 'work_section_passenger_point5_label'),
            'work_section_passenger_point5_image' => $this->data($request, $language, 'work_section_passenger_point5_image'),
            'work_section_passenger_point5_description' => $this->data($request, $language, 'work_section_passenger_point5_description'),
            'work_section_driver_label' => $this->data($request, $language, 'work_section_driver_label'),
            'work_section_driver_description' => $this->data($request, $language, 'work_section_driver_description'),
            'work_section_driver_point1_label' => $this->data($request, $language, 'work_section_driver_point1_label'),
            'work_section_driver_point1_image' => $this->data($request, $language, 'work_section_driver_point1_image'),
            'work_section_driver_point1_description' => $this->data($request, $language, 'work_section_driver_point1_description'),
            'work_section_driver_point2_label' => $this->data($request, $language, 'work_section_driver_point2_label'),
            'work_section_driver_point2_image' => $this->data($request, $language, 'work_section_driver_point2_image'),
            'work_section_driver_point2_description' => $this->data($request, $language, 'work_section_driver_point2_description'),
            'work_section_driver_point3_label' => $this->data($request, $language, 'work_section_driver_point3_label'),
            'work_section_driver_point3_image' => $this->data($request, $language, 'work_section_driver_point3_image'),
            'work_section_driver_point3_description' => $this->data($request, $language, 'work_section_driver_point3_description'),
            'work_section_driver_point4_label' => $this->data($request, $language, 'work_section_driver_point4_label'),
            'work_section_driver_point4_image' => $this->data($request, $language, 'work_section_driver_point4_image'),
            'work_section_driver_point4_description' => $this->data($request, $language, 'work_section_driver_point4_description'),
            'work_section_driver_point5_label' => $this->data($request, $language, 'work_section_driver_point5_label'),
            'work_section_driver_point5_image' => $this->data($request, $language, 'work_section_driver_point5_image'),
            'work_section_driver_point5_description' => $this->data($request, $language, 'work_section_driver_point5_description'),
            'doing_section_main_heading' => $this->data($request, $language, 'doing_section_main_heading'),
            'doing_section_main_text' => $this->data($request, $language, 'doing_section_main_text'),
            'doing_section_slider_image' => $this->data($request, $language, 'doing_section_slider_image'),
            'doing_section_label1' => $this->data($request, $language, 'doing_section_label1'),
            'doing_section_label2' => $this->data($request, $language, 'doing_section_label2'),
            'reasons_section_main_heading' => $this->data($request, $language, 'reasons_section_main_heading'),
            'reasons_section_main_text' => $this->data($request, $language, 'reasons_section_main_text'),
            'reasons_section_members_label' => $this->data($request, $language, 'reasons_section_members_label'),
            'reasons_section_members_image' => $this->data($request, $language, 'reasons_section_members_image'),
            'reasons_section_members_description' => $this->data($request, $language, 'reasons_section_members_description'),
            'reasons_section_driver_label' => $this->data($request, $language, 'reasons_section_driver_label'),
            'reasons_section_driver_image' => $this->data($request, $language, 'reasons_section_driver_image'),
            'reasons_section_driver_description' => $this->data($request, $language, 'reasons_section_driver_description'),
            'reasons_section_quality_label' => $this->data($request, $language, 'reasons_section_quality_label'),
            'reasons_section_quality_image' => $this->data($request, $language, 'reasons_section_quality_image'),
            'reasons_section_quality_description' => $this->data($request, $language, 'reasons_section_quality_description'),
            'reasons_section_policy_label' => $this->data($request, $language, 'reasons_section_policy_label'),
            'reasons_section_policy_image' => $this->data($request, $language, 'reasons_section_policy_image'),
            'reasons_section_students_image' => $this->data($request, $language, 'reasons_section_students_image'),
            'reasons_section_policy_description' => $this->data($request, $language, 'reasons_section_policy_description'),
            'reasons_section_students_label' => $this->data($request, $language, 'reasons_section_students_label'),
            'reasons_section_students_description' => $this->data($request, $language, 'reasons_section_students_description'),
            'reasons_section_safety_label' => $this->data($request, $language, 'reasons_section_safety_label'),
            'reasons_section_safety_image' => $this->data($request, $language, 'reasons_section_safety_image'),
            'reasons_section_safety_description' => $this->data($request, $language, 'reasons_section_safety_description'),
            'reasons_section_price_label' => $this->data($request, $language, 'reasons_section_price_label'),
            'reasons_section_price_image' => $this->data($request, $language, 'reasons_section_price_image'),
            'reasons_section_price_description' => $this->data($request, $language, 'reasons_section_price_description'),
            'reasons_section_use_label' => $this->data($request, $language, 'reasons_section_use_label'),
            'reasons_section_use_image' => $this->data($request, $language, 'reasons_section_use_image'),
            'reasons_section_use_description' => $this->data($request, $language, 'reasons_section_use_description'),
            'reasons_section_reliable_label' => $this->data($request, $language, 'reasons_section_reliable_label'),
            'reasons_section_reliable_image' => $this->data($request, $language, 'reasons_section_reliable_image'),
            'reasons_section_reliable_description' => $this->data($request, $language, 'reasons_section_reliable_description'),
            'reasons_section_responsible_label' => $this->data($request, $language, 'reasons_section_responsible_label'),
            'reasons_section_responsible_image' => $this->data($request, $language, 'reasons_section_responsible_image'),
            'reasons_section_responsible_description' => $this->data($request, $language, 'reasons_section_responsible_description'),
            'movement_section_heading' => $this->data($request, $language, 'movement_section_heading'),
            'movement_section_icon' => $this->data($request, $language, 'movement_section_icon'),
            'movement_section_text' => $this->data($request, $language, 'movement_section_text'),
            'members_section_heading' => $this->data($request, $language, 'members_section_heading'),
            'members_section_text' => $this->data($request, $language, 'members_section_text'),
            'news_section_heading' => $this->data($request, $language, 'news_section_heading'),
            'news_section_icon1' => $this->data($request, $language, 'news_section_icon1'),
            'news_section_icon2' => $this->data($request, $language, 'news_section_icon2'),
            'news_section_icon3' => $this->data($request, $language, 'news_section_icon3'),
            'news_section_icon4' => $this->data($request, $language, 'news_section_icon4'),
            'use_section_heading' => $this->data($request, $language, 'use_section_heading'),
            'use_section_text' => $this->data($request, $language, 'use_section_text'),
            'use_section_point1_label' => $this->data($request, $language, 'use_section_point1_label'),
            'use_section_point1_image' => $this->data($request, $language, 'use_section_point1_image'),
            'use_section_point1_description' => $this->data($request, $language, 'use_section_point1_description'),
            'use_section_point2_label' => $this->data($request, $language, 'use_section_point2_label'),
            'use_section_point2_image' => $this->data($request, $language, 'use_section_point2_image'),
            'use_section_point2_description' => $this->data($request, $language, 'use_section_point2_description'),
            'use_section_point3_label' => $this->data($request, $language, 'use_section_point3_label'),
            'use_section_point3_image' => $this->data($request, $language, 'use_section_point3_image'),
            'use_section_point3_description' => $this->data($request, $language, 'use_section_point3_description'),
            'use_section_point4_label' => $this->data($request, $language, 'use_section_point4_label'),
            'use_section_point4_image' => $this->data($request, $language, 'use_section_point4_image'),
            'use_section_point4_description' => $this->data($request, $language, 'use_section_point4_description'),
            'reliability_section_heading' => $this->data($request, $language, 'reliability_section_heading'),
            'reliability_section_text' => $this->data($request, $language, 'reliability_section_text'),
            'reliability_section_passengers_label' => $this->data($request, $language, 'reliability_section_passengers_label'),
            'reliability_section_passengers_description' => $this->data($request, $language, 'reliability_section_passengers_description'),
            'reliability_section_drivers_label' => $this->data($request, $language, 'reliability_section_drivers_label'),
            'reliability_section_drivers_description' => $this->data($request, $language, 'reliability_section_drivers_description'),
            'reliability_section_button_label1' => $this->data($request, $language, 'reliability_section_button_label1'),
            'reliability_section_button_label2' => $this->data($request, $language, 'reliability_section_button_label2'),
            'payment_section_heading' => $this->data($request, $language, 'payment_section_heading'),
            'payment_section_text' => $this->data($request, $language, 'payment_section_text'),
            'payment_section_icon1' => $this->data($request, $language, 'payment_section_icon1'),
            'payment_section_icon2' => $this->data($request, $language, 'payment_section_icon2'),
            'payment_section_icon3' => $this->data($request, $language, 'payment_section_icon3'),
            'payment_section_icon4' => $this->data($request, $language, 'payment_section_icon4'),
        ];
    }

    public function update($homePageSetting, $language, $request)
    {
        $fields = $this->fields($homePageSetting, $language, $request);
        $homePageSettingDetail = HomePageSettingDetail::whereHomePageSettingId($homePageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$homePageSettingDetail){
            $fields = $this->fields($homePageSetting, $language, $request);
        HomePageSettingDetail::create($fields);
        }
        else{

            HomePageSettingDetail::whereHomePageSettingId($homePageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
