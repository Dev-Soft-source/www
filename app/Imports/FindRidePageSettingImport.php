<?php

namespace App\Imports;

use App\Models\FindRidePageSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FindRidePageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','pink_ride_page_heading','extra_care_ride_page_label','pink_ride_page_label', 'pink_ride_description', 'pink_ride_page_faq_heading','extra_care_ride_faqs_heading', 'search_results_pink_ride_label','search_results_extra_care_ride_label','more_rides_pink_ride_label','to_pink_ride_label','imp_pink_ride_label','imp_extra_care_ride_label','navbar_icon','from_field_icon','swap_field_icon','to_field_icon','date_field_icon','search_field_icon','search_section_from_placeholder','search_section_to_placeholder','search_section_date_placeholder','search_section_required_error','search_section_keyword_label','search_section_keyword_placeholder','search_section_button_label','search_section_recent_searches','card_section_from_label','card_section_to_label','card_section_at_label','card_section_seats_left','card_section_per_seat','heading_ride_card_section','card_section_booked','card_section_seats','card_section_booking_fee','card_section_seats_fee','card_section_amount','card_section_driver','card_section_age','card_section_driven','card_section_passengers','card_section_review','card_section_completed','trips_card_section_seat_booked','trips_card_section_seat_available','trips_card_section_review_driver','firm_cancellation_tooltip','filter_section_heading','filter1_driver_heading','driver_age_label','driver_age_placeholder','driver_rating_label','driver_rating_placeholder','driver_phone_access_label','driver_know_label','driver_know_placeholder','filter2_passengers_heading','passengers_rating_label','passengers_rating_placeholder','filter3_payment_methods_heading','apply_button_label','clear_button_label','payment_methods_label','payment_methods_option1','filter4_vehicle_heading','vehicle_type_label','vehicle_type_placeholder','ride_features_option17','luggage_label','luggage_placeholder','smoking_label','pets_allowed_label','card_section_cancelled','search_filter_all_label','search_filter_select_vehicle_label','card_section_not_live','card_section_booking_request','trips_card_section_reviewed','card_section_no_review','search_result_load_more_btn','search_result_no_more_data_message','search_result_no_found_message','search_result_label','filter_what_label','search_and_above_label','ride_preferences_label','search_section_pink_ride_label','search_section_extra_care_label','filter_search_btn_label','filter_close_btn_label','hide_ride_popup_heading','hide_ride_popup_text','hide_ride_popup_confirm_button','hide_ride_popup_take_me_back_button'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = FindRidePageSetting::first();
        if (!$setting) {
            $setting = FindRidePageSetting::create([]);
        }
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Find Ride Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Find Ride Page Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($setting, $row);
            }
        } else {
            if ($this->languageId !== null) {
                $this->processMultiColumnFormat($setting, $firstRow);
            }
        }
    }

    protected function processAllLanguagesFormat(FindRidePageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->fieldsList();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = isset($row[$fieldNameKey]) ? strtolower(trim((string) $row[$fieldNameKey])) : null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) {
                continue;
            }
            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim($col));
                if (!isset($nameToId[$langKey])) {
                    continue;
                }
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;
                $detail = FindRidePageSettingDetail::firstOrCreate(
                    [
                        'find_ride_page_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    []
                );
                $detail->$fieldName = $value;
                $detail->save();
            }
        }
    }

    protected function processSingleColumnFormat($setting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;
        if (empty($fieldName) || $value === null || $value === '') return;
        $fieldName = strtolower(trim($fieldName));
        if (!in_array($fieldName, $this->fieldsList())) return;

        $detail = FindRidePageSettingDetail::where('find_ride_page_setting_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            FindRidePageSettingDetail::create([
                'find_ride_page_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'find_ride_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        FindRidePageSettingDetail::updateOrCreate(
            [
                'find_ride_page_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $fields
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
        ];
    }
}


