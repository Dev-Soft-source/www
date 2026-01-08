<?php

namespace App\Imports;

use App\Models\PostRidePageSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\PostRidePageSettingSubDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PostRidePageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function detailFields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','post_arrived_again_label','ride_info_heading','from_label','from_placeholder','to_label','to_placeholder','pick_up_label','pick_up_placeholder','drop_off_label','drop_off_placeholder','date_time_label','at_label','recurring_label','recurring_type_label','recurring_trips_label','recurring_trips_placeholder','meeting_drop_off_description_label','meeting_drop_off_description_placeholder','seats_label','seats_middle_label','seats_back_label','vehicle_label','skip_label','add_vehicle_label','existing_label','make_label','make_placeholder','model_label','model_placeholder','type_label','year_label','color_label','liscense_label','car_type_label','electric_car_label','hybrid_car_label','gas_car_label','preferences_label','smoking_label','animals_label','features_label','features_option17','booking_label','max_back_seats_label','luggage_label','luggage_checkbox_label1','luggage_checkbox_label1_tooltip','price_payment_heading','price_per_seat_label','payment_methods_label','cancellation_policy_label','anything_to_add_label','anything_to_add_placeholder','disclaimers_label','app_disclaimers_description1','app_disclaimers_description2','app_disclaimers_description3','app_disclaimers_description4','disclaimers_description','agree_terms_label','submit_button_label','main_heading_update','mobile_agree_terms_label','mobile_term_of_service_label','mobile_agree_terms_and_label','mobile_term_of_use_label','update_button_label','indicates_required_field_text','navbar_icon','repost_ride_btn_label','city_not_in_record','pink_ride_tooltip_only_text','pink_ride_tooltip_female_text','pink_ride_tooltip_complete_profile_text','pink_ride_tooltip_driver_text','pink_ride_tooltip_with_text','pink_ride_tooltip_phone_number_text','pink_ride_tooltip_email_text','pink_ride_tooltip_driver_license_text','pink_ride_tooltip_verified_text','pink_ride_tooltip_select_this_ride_text','extra_care_tooltip_driver_review_text','extra_care_tooltip_greater_age_text','extra_care_tooltip_greater_text','extra_care_tooltip_eligible_text','extra_care_tooltip_complete_profile_text','extra_care_tooltip_verified_text','extra_care_tooltip_driver_license_text','extra_care_tooltip_phone_number_text','extra_care_tooltip_email_text','extra_care_tooltip_and_his_text','select_vehicle_type','vehicle_type_placeholder','seat_text','recurring_type_select_placeholder','recurring_type_daily_label','recurring_type_weekly_label','post_ride_again_main_heading','upcoming_label','completed_label','cancelled_label','cancelled_ride_no_found_message','completed_ride_no_found_message','upcoming_ride_no_found_message','extra_care_tooltip_admin_enable_text','extra_care_tooltip_admin_disable_text','pink_ride_tooltip_admin_enable_text','pink_ride_tooltip_admin_disable_text'
        ];
    }

    protected function subFields(): array
    {
        return ['city_not_fount_contact_text','extra_care_popup_eligible_text','feilds_required_text'];
    }

    public function collection(Collection $rows)
    {
        $setting = PostRidePageSetting::first() ?? PostRidePageSetting::create([]);
        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingle = isset($keys[0]) && in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (in_array($k, $this->detailFields()) || in_array($k, $this->subFields())) {
                    $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
                }
            }
        } else {
            $data = $firstRow->toArray();
        }

        $detailPayload = [
            'post_ride_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->detailFields() as $f) { $detailPayload[$f] = $data[$f] ?? null; }
        PostRidePageSettingDetail::updateOrCreate(
            ['post_ride_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $detailPayload
        );

        $subPayload = [
            'post_ride_page_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->subFields() as $f) { $subPayload[$f] = $data[$f] ?? null; }
        PostRidePageSettingSubDetail::updateOrCreate(
            ['post_ride_page_id' => $setting->id, 'language_id' => $this->languageId],
            $subPayload
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'ride_info_heading' => 'required|string',
            'from_label' => 'required|string',
            'to_label' => 'required|string',
            'pick_up_label' => 'required|string',
            'drop_off_label' => 'required|string',
            'date_time_label' => 'required|string',
            'seats_label' => 'required|string',
            'vehicle_label' => 'required|string',
            'preferences_label' => 'required|string',
            'booking_label' => 'required|string',
            'luggage_label' => 'required|string',
            'price_payment_heading' => 'required|string',
            'payment_methods_label' => 'required|string',
            'disclaimers_label' => 'required|string',
            'agree_terms_label' => 'required|string',
            'submit_button_label' => 'required|string',
            'navbar_icon' => 'required|string',
        ];
    }
}


