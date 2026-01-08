<?php

namespace App\Imports;

use App\Models\RideDetailPageSetting;
use App\Models\RideDetailPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RideDetailPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        // Only include fields that exist in RideDetailPageSettingService::fields() and the database
        return [
            'name','meta_keywords','meta_description','main_heading','from_label','to_label','at_label','co_passenger_label','ride_co_passenger_heading','trip_co_passenger_heading','payment_method_label','booking_type_label','cancellation_policy_label','luggage_label','smoking_label','pets_label','seats_left_label','per_seat_label','ride_features_label','ride_seat_label','all_seats_booked_label','ride_canceller_by_driver','ride_completed_text','book_seat_btn_label','no_seat_available_label','no_ride_found_message','cancel_booking_btn_label','cancel_ride_btn_label','cancel_ride_confirmation','cancel_ride_yes_btn','cancel_ride_no_btn','edit_ride_btn_label','review_label','booking_request_heading','seat_requested_label','request_accept_label','request_reject_label','secured_cash_heading','enter_code_label','mobile_seat_booked_heading','mobile_seat_booked_label','mobile_seat_fare_label','mobile_seat_booking_fee_label','mobile_seat_total_amount_label','vehicle_info_label','driver_info_label','review_driver_info_label','review_passanger_label','driver_chat_with','driver_label','cancellation_policy','passengers_driven_label','driver_age_label','driver_chat_heading','driver_chat_label','driver_chat_button_label','booking_table_heading','passenger_column_label','seat_booked_column_label','total_cost_column_label','booked_on_column_label','status_column_label','booking_requested_status_label','seat_booked_status_label','booking_denied_status_label','actions_column_label','edit_button_actions_label','review_button_label','i_reviewed_label','noon_label','midnight_label','driver_note_label','trip_main_heading','ride_main_heading','discount_label','booking_request_main_heading','passenger_age_label','passenger_gender_label','seat_on_column_label','cancellation_policy_tooltip','cancellation_policy_tooltip_url','pickup_dropoff_info_heading','pickup_label','dropoff_label','description_label','verified_email','verified_phone','instant_btn_label','chat_error_message','empty_chat_placeholder'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = RideDetailPageSetting::first() ?? RideDetailPageSetting::create([]);
        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingle = isset($keys[0]) && in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (!in_array($k, $this->fields())) continue;
                $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
            }
        } else {
            $data = $firstRow->toArray();
        }

        $payload = [
            'ride_detail_page_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        RideDetailPageSettingDetail::updateOrCreate(
            ['ride_detail_page_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
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
            'from_label' => 'required|string',
            'to_label' => 'required|string',
            // many more required in service; Excel 422 handler will list any missing
        ];
    }
}


