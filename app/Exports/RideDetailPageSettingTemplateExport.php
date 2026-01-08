<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RideDetailPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        $fields = $this->getFields();
        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->getFields());
    }

    protected function getFields(): array
    {
        // Only include fields that exist in RideDetailPageSettingService::fields() and the database
        return [
            'name','meta_keywords','meta_description','main_heading','from_label','to_label','at_label','co_passenger_label','ride_co_passenger_heading','trip_co_passenger_heading','payment_method_label','booking_type_label','cancellation_policy_label','luggage_label','smoking_label','pets_label','seats_left_label','per_seat_label','ride_features_label','ride_seat_label','all_seats_booked_label','ride_canceller_by_driver','ride_completed_text','book_seat_btn_label','no_seat_available_label','no_ride_found_message','cancel_booking_btn_label','cancel_ride_btn_label','cancel_ride_confirmation','cancel_ride_yes_btn','cancel_ride_no_btn','edit_ride_btn_label','review_label','booking_request_heading','seat_requested_label','request_accept_label','request_reject_label','secured_cash_heading','enter_code_label','mobile_seat_booked_heading','mobile_seat_booked_label','mobile_seat_fare_label','mobile_seat_booking_fee_label','mobile_seat_total_amount_label','vehicle_info_label','driver_info_label','review_driver_info_label','review_passanger_label','driver_chat_with','driver_label','cancellation_policy','passengers_driven_label','driver_age_label','driver_chat_heading','driver_chat_label','driver_chat_button_label','booking_table_heading','passenger_column_label','seat_booked_column_label','total_cost_column_label','booked_on_column_label','status_column_label','booking_requested_status_label','seat_booked_status_label','booking_denied_status_label','actions_column_label','edit_button_actions_label','review_button_label','i_reviewed_label','noon_label','midnight_label','driver_note_label','trip_main_heading','ride_main_heading','discount_label','booking_request_main_heading','passenger_age_label','passenger_gender_label','seat_on_column_label','cancellation_policy_tooltip','cancellation_policy_tooltip_url','pickup_dropoff_info_heading','pickup_label','dropoff_label','description_label','verified_email','verified_phone','instant_btn_label','chat_error_message','empty_chat_placeholder'
        ];
    }
}


