<?php

namespace App\Imports;

use App\Models\PostRidePageSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\PostRidePageSettingSubDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PostRidePageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function detailFields(): array
    {
        $fields = [
            'name','meta_keywords','meta_description','main_heading','post_arrived_again_label','ride_info_heading','from_label','from_placeholder','to_label','to_placeholder','pick_up_label','pick_up_placeholder','drop_off_label','drop_off_placeholder','stop_along_the_way_label','stops_along_the_way_label','add_stop_btn_label','stop_suggest_label','stop_placeholder','pickup_off_placeholder','delete_stop_text','stops_remove_confirm_text','distance_suffix','date_time_label','at_label','recurring_label','recurring_type_label','recurring_trips_label','recurring_trips_placeholder','meeting_drop_off_description_label','meeting_drop_off_description_placeholder','seats_label','seats_middle_label','seats_back_label','vehicle_label','skip_label','add_vehicle_label','existing_label','make_placeholder','model_placeholder','preferences_label','smoking_label','animals_label','features_label','features_option17','booking_label','max_back_seats_label','luggage_label','luggage_checkbox_label1','price_payment_heading','price_per_seat_label','payment_methods_label','cancellation_policy_label','anything_to_add_label','anything_to_add_placeholder','disclaimers_label','app_disclaimers_description1','app_disclaimers_description2','app_disclaimers_description3','app_disclaimers_description4','disclaimers_description','pink_ride_disclaimers_description','extra_care_ride_disclaimers_description','agree_terms_label','agree_term_error','carpool_regulation_limit_message','max_price_per_seat_message','non_commercial_carpool_requirement_message','price_error_heading','price_error_adjust_btn_label','delete_stop_modal_no_btn','delete_stop_modal_yes_btn','price_warning_heading','price_warning_adjust_btn_label','price_warning_keep_current_btn_label','price_above_reimbursement_warning','price_reduction_suggestion_message','seats_warning_modal_heading','seats_warning_modal_paragraph','seats_warning_modal_got_it_btn','seats_warning_modal_learn_more_btn','phone_required_modal_heading','phone_required_modal_body_before','phone_required_modal_link_text','phone_required_modal_body_after','phone_required_modal_close_btn','phone_required_modal_phone_btn','alert_need_government_photo_label','alert_need_driver_license_label','price_error_paragraph_1','price_error_paragraph_2','price_error_paragraph_3','submit_button_label','main_heading_update','mobile_agree_terms_label','mobile_term_of_service_label','mobile_agree_terms_and_label','mobile_term_of_use_label','update_button_label','indicates_required_field_text','navbar_icon','repost_ride_btn_label','city_not_in_record','pink_ride_tooltip_only_text','pink_ride_tooltip_female_text','pink_ride_tooltip_complete_profile_text','pink_ride_tooltip_driver_text','pink_ride_tooltip_with_text','pink_ride_tooltip_phone_number_text','pink_ride_tooltip_email_text','pink_ride_tooltip_driver_license_text','pink_ride_tooltip_verified_text','pink_ride_tooltip_select_this_ride_text','extra_care_tooltip_driver_review_text','extra_care_tooltip_greater_age_text','extra_care_tooltip_greater_text','extra_care_tooltip_eligible_text','extra_care_tooltip_complete_profile_text','extra_care_tooltip_verified_text','extra_care_tooltip_driver_license_text','extra_care_tooltip_phone_number_text','extra_care_tooltip_email_text','extra_care_tooltip_and_his_text','select_vehicle_type','select_vehicle','vehicle_type_placeholder','seat_text','seats_text','recurring_type_select_placeholder','recurring_type_daily_label','recurring_type_weekly_label','post_ride_again_main_heading','upcoming_label','completed_label','cancelled_label','cancelled_ride_no_found_message','completed_ride_no_found_message','upcoming_ride_no_found_message','update_ride_label','pink_ride_disclaimer_text','extra_care_ride_disclaimer_text','extra_care_tooltip_admin_enable_text','extra_care_tooltip_admin_disable_text','pink_ride_tooltip_admin_enable_text','pink_ride_tooltip_admin_disable_text'
        ];

        return array_values(array_diff($fields, [
            'features_option17',
            'luggage_checkbox_label1',
            'cancellation_policy_label',
        ]));
    }

    protected function subFields(): array
    {
        return [];
    }

    public function collection(Collection $rows)
    {
        $setting = PostRidePageSetting::first() ?? PostRidePageSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Post Ride Page Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Post Ride Page Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingle = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle && $this->languageId !== null) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (in_array($k, $this->detailFields()) || in_array($k, $this->subFields())) {
                    $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
                }
            }
            $this->applyData($setting, $data);
        } elseif ($this->languageId !== null) {
            $data = $firstRow->toArray();
            $this->applyData($setting, $data);
        }
    }

    protected function processAllLanguagesFormat(PostRidePageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : (in_array('field name', $headers) ? 'field name' : 'Field Name');
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $detailFields = $this->detailFields();
        $subFields = $this->subFields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = isset($row[$fieldNameKey]) ? strtolower(trim((string) $row[$fieldNameKey])) : null;
            if (empty($fieldName)) continue;
            $isDetail = in_array($fieldName, $detailFields, true);
            $isSub = in_array($fieldName, $subFields, true);
            if (!$isDetail && !$isSub) continue;

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim($col));
                if (!isset($nameToId[$langKey])) continue;
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                if ($isDetail) {
                    $detail = PostRidePageSettingDetail::firstOrCreate(
                        ['post_ride_page_setting_id' => $setting->id, 'language_id' => $languageId],
                        []
                    );
                    $detail->$fieldName = $value;
                    $detail->save();
                } else {
                    $subDetail = PostRidePageSettingSubDetail::firstOrCreate(
                        ['post_ride_page_id' => $setting->id, 'language_id' => $languageId],
                        []
                    );
                    $subDetail->$fieldName = $value;
                    $subDetail->save();
                }
            }
        }
    }

    protected function applyData(PostRidePageSetting $setting, array $data): void
    {
        $detailPayload = [
            'post_ride_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->detailFields() as $f) {
            $detailPayload[$f] = $data[$f] ?? null;
        }
        PostRidePageSettingDetail::updateOrCreate(
            ['post_ride_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $detailPayload
        );

        $subPayload = [
            'post_ride_page_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->subFields() as $f) {
            $subPayload[$f] = $data[$f] ?? null;
        }
        PostRidePageSettingSubDetail::updateOrCreate(
            ['post_ride_page_id' => $setting->id, 'language_id' => $this->languageId],
            $subPayload
        );
    }

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') {
            return [];
        }
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
            'agree_term_error' => 'required|string',
            'carpool_regulation_limit_message' => 'required|string',
            'max_price_per_seat_message' => 'required|string',
            'non_commercial_carpool_requirement_message' => 'required|string',
            'price_error_heading' => 'required|string',
            'price_error_adjust_btn_label' => 'required|string',
            'submit_button_label' => 'required|string',
            'navbar_icon' => 'required|string',
        ];
    }
}


