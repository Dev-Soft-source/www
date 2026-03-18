<?php

namespace App\Imports;

use App\Models\TripsPageSetting;
use App\Models\TripsPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TripsPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
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
            'name','meta_keywords','meta_description','passenger_trips_heading','driver_rides_heading','upcoming_label','no_upcoming_trips_label','no_upcoming_rides_label','completed_label','no_completed_trips_label','no_completed_rides_label','cancelled_label','no_cancelled_trips_label','no_cancelled_rides_label','timeliness_label','safety_label','respect_and_courtesy_label','personal_hygiene_label','overall_attitude_label','communication_label','comfort_label','conscious_passenger_wellness_label','condition_label','review_criteria_label','main_heading','average_label','load_more_trips_label','no_more_data_message','load_more_rides_label','review_passengers_review_label','review_passengers_i_review_label','review_passengers_heading','passenger_cancel_ride_btn_label','booking_cancel_btn_label','cancel_booking_trip_placeholder','cancel_all_feilds_are_required','cancel_ride_label','cancel_ride_placeholder','cancel_seat_label','number_of_seat_booked','cancel_booking_heading','cancel_booking_main_heading','cancel_ride_setting','tell_passenger_why_label','tell_passenger_why_placeholder','confirm_cancel_ride','remove_from_this_ride_message','remove_passenger_and_block_message','remove_day_label','remove_day_error','driver_remove_reason_placeholder','passenger_remove_reason_placeholder','passenger_review_heading','driver_review_heading','passenger_review_placeholder','driver_review_placeholder','review_submit_btn_label','remove_passenger_heading','remove_passenger_text','block_temporarily_label','block_permanently_label','remove_day_placeholder','driver_remove_reason_label','driver_remove_reason_error','passenger_remove_reason_label','passenger_remove_reason_error','passenger_cancel_sure_message','cancel_message_title','cancel_booking_confirm_message','booking_cancel_btn_yes_label','booking_cancel_btn_no_label','cancel_booking_confirm_firm_message','cancel_ride_confirm_decision_title','cancel_ride_confirm_ok_btn_text','cancel_ride_confirm_modal_title','cancel_ride_confirm_modal_message','cancel_ride_confirm_modal_no_btn_text','cancel_ride_confirm_modal_yes_btn_text','cancel_booking_confirm_48_hour_message','cancel_booking_confirm_12_to_48_hour_message','cancel_booking_confirm_less_12_hour_message'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = TripsPageSetting::first() ?? TripsPageSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Trips Page Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Trips Page Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            $data = [];
            foreach ($rows as $row) {
                $name = strtolower(trim($row['field_name'] ?? ''));
                if (!$name || !in_array($name, $this->fieldsList())) continue;
                $data[$name] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($setting, $data);
        } elseif ($this->languageId !== null) {
            $this->applyData($setting, $firstRow->toArray());
        }
    }

    protected function processAllLanguagesFormat(TripsPageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : (in_array('field name', $headers) ? 'field name' : 'Field Name');
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
                $detail = TripsPageSettingDetail::firstOrCreate(
                    [
                        'trips_page_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    []
                );
                $detail->$fieldName = $value;
                $detail->save();
            }
        }
    }

    protected function applyData($setting, array $data): void
    {
        $payload = [
            'trips_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) { $payload[$f] = $data[$f] ?? null; }

        TripsPageSettingDetail::updateOrCreate(
            [
                'trips_page_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $payload
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
            'passenger_trips_heading' => 'required|string',
            'driver_rides_heading' => 'required|string',
            'upcoming_label' => 'required|string',
            'completed_label' => 'required|string',
            'cancelled_label' => 'required|string',
        ];
    }
}


