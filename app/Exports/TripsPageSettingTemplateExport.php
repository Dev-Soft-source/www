<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TripsPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        return [
            'name','meta_keywords','meta_description','passenger_trips_heading','driver_rides_heading','upcoming_label','no_upcoming_trips_label','no_upcoming_rides_label','completed_label','no_completed_trips_label','no_completed_rides_label','cancelled_label','no_cancelled_trips_label','no_cancelled_rides_label','timeliness_label','safety_label','respect_and_courtesy_label','personal_hygiene_label','overall_attitude_label','communication_label','comfort_label','conscious_passenger_wellness_label','condition_label','review_criteria_label','main_heading','average_label','load_more_trips_label','no_more_data_message','load_more_rides_label','review_passengers_review_label','review_passengers_i_review_label','review_passengers_heading','passenger_cancel_ride_btn_label','booking_cancel_btn_label','cancel_booking_trip_placeholder','cancel_all_feilds_are_required','cancel_ride_label','cancel_ride_placeholder','cancel_seat_label','number_of_seat_booked','cancel_booking_heading','cancel_booking_main_heading','cancel_ride_setting','tell_passenger_why_label','tell_passenger_why_placeholder','confirm_cancel_ride','remove_from_this_ride_message','remove_passenger_and_block_message','remove_day_label','remove_day_error','driver_remove_reason_placeholder','passenger_remove_reason_placeholder','passenger_review_heading','driver_review_heading','passenger_review_placeholder','driver_review_placeholder','review_submit_btn_label','remove_passenger_heading','remove_passenger_text','block_temporarily_label','block_permanently_label','remove_day_placeholder','driver_remove_reason_label','driver_remove_reason_error','passenger_remove_reason_label','passenger_remove_reason_error','passenger_cancel_sure_message','cancel_message_title','cancel_booking_confirm_message','booking_cancel_btn_yes_label','booking_cancel_btn_no_label','cancel_booking_confirm_firm_message','cancel_booking_confirm_48_hour_message','cancel_booking_confirm_12_to_48_hour_message','cancel_booking_confirm_less_12_hour_message'
        ];
    }
}


