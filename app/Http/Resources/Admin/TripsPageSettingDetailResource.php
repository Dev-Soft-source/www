<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TripsPageSettingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'trips_page_setting_id' => $this->trips_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'passenger_trips_heading' => $this->passenger_trips_heading,
            'driver_rides_heading' => $this->driver_rides_heading,
            'upcoming_label' => $this->upcoming_label,
            'no_upcoming_trips_label' => $this->no_upcoming_trips_label,
            'no_upcoming_rides_label' => $this->no_upcoming_rides_label,
            'completed_label' => $this->completed_label,
            'no_completed_trips_label' => $this->no_completed_trips_label,
            'no_completed_rides_label' => $this->no_completed_rides_label,
            'cancelled_label' => $this->cancelled_label,
            'no_cancelled_trips_label' => $this->no_cancelled_trips_label,
            'no_cancelled_rides_label' => $this->no_cancelled_rides_label,
            'timeliness_label' => $this->timeliness_label,
            'safety_label' => $this->safety_label,
            'respect_and_courtesy_label' => $this->respect_and_courtesy_label,
            'personal_hygiene_label' => $this->personal_hygiene_label,
            'overall_attitude_label' => $this->overall_attitude_label,
            'communication_label' => $this->communication_label,
            'comfort_label' => $this->comfort_label,
            'conscious_passenger_wellness_label' => $this->conscious_passenger_wellness_label,
            'condition_label' => $this->condition_label,
            'review_criteria_label' => $this->review_criteria_label,
            'main_heading' => $this->main_heading,
            'average_label' => $this->average_label,
            'load_more_trips_label' => $this->load_more_trips_label,
            'no_more_data_message' => $this->no_more_data_message,
            'load_more_rides_label' => $this->load_more_rides_label,
            'review_passengers_review_label' => $this->review_passengers_review_label,
            'review_passengers_i_review_label' => $this->review_passengers_i_review_label,
            'review_passengers_heading' => $this->review_passengers_heading,
            'passenger_cancel_ride_btn_label' => $this->passenger_cancel_ride_btn_label,
            'booking_cancel_btn_label' => $this->booking_cancel_btn_label,
            'cancel_booking_trip_placeholder' => $this->cancel_booking_trip_placeholder,
            'cancel_all_feilds_are_required' => $this->cancel_all_feilds_are_required,
            'cancel_ride_label' => $this->cancel_ride_label,
            'cancel_ride_placeholder' => $this->cancel_ride_placeholder,
            'cancel_seat_label' => $this->cancel_seat_label,
            'number_of_seat_booked' => $this->number_of_seat_booked,
            'cancel_booking_heading' => $this->cancel_booking_heading,
            'cancel_booking_main_heading' => $this->cancel_booking_main_heading,
            'cancel_ride_setting' => $this->cancel_ride_setting,
            'tell_passenger_why_label' => $this->tell_passenger_why_label,
            'tell_passenger_why_placeholder' => $this->tell_passenger_why_placeholder,
            'Confirm_cancel_ride' => $this->Confirm_cancel_ride,
            'remove_from_this_ride_message' => $this->remove_from_this_ride_message,
            'remove_passenger_and_block_message' => $this->remove_passenger_and_block_message,
            'remove_day_label' => $this->remove_day_label,
            'remove_day_error' => $this->remove_day_error,
            'driver_remove_reason_placeholder' => $this->driver_remove_reason_placeholder,
            'passenger_remove_reason_placeholder' => $this->passenger_remove_reason_placeholder,
            'passenger_review_heading' => $this->passenger_review_heading,
            'driver_review_heading' => $this->driver_review_heading,
            'passenger_review_placeholder' => $this->passenger_review_placeholder,
            'driver_review_placeholder' => $this->driver_review_placeholder,
            'review_submit_btn_label' => $this->review_submit_btn_label,
            'remove_passenger_heading' => $this->remove_passenger_heading,
            'remove_passenger_text' => $this->remove_passenger_text,
            'block_temporarily_label' => $this->block_temporarily_label,
            'block_permanently_label' => $this->block_permanently_label,
            'remove_day_placeholder' => $this->remove_day_placeholder,
            'driver_remove_reason_label' => $this->driver_remove_reason_label,
            'driver_remove_reason_error' => $this->driver_remove_reason_error,
            'passenger_remove_reason_label' => $this->passenger_remove_reason_label,
            'passenger_remove_reason_error' => $this->passenger_remove_reason_error,
            'passenger_cancel_sure_message' => $this->passenger_cancel_sure_message,
            'cancel_message_title' => $this->cancel_message_title,
            'cancel_booking_confirm_message' => $this->cancel_booking_confirm_message,
            'booking_cancel_btn_yes_label' => $this->booking_cancel_btn_yes_label,
            'booking_cancel_btn_no_label' => $this->booking_cancel_btn_no_label,
            'cancel_booking_confirm_firm_message' => $this->cancel_booking_confirm_firm_message,
            'cancel_booking_confirm_48_hour_message' => $this->cancel_booking_confirm_48_hour_message,
            'cancel_booking_confirm_12_to_48_hour_message' => $this->cancel_booking_confirm_12_to_48_hour_message,
            'cancel_booking_confirm_less_12_hour_message' => $this->cancel_booking_confirm_less_12_hour_message,
            'trips_page_setting' => new TripsPageSettingResource($this->whenLoaded('tripsPageSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
