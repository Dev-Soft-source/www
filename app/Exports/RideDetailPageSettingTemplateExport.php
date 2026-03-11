<?php

namespace App\Exports;

use App\Models\RideDetailPageSetting;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RideDetailPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\RideDetailPageSetting|null */
    protected $existingData;

    /**
     * @param string $format 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages For all_languages format
     * @param \App\Models\RideDetailPageSetting|null $existingData For all_languages (with rideDetailPageSettingDetail loaded)
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public function collection(): Collection
    {
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }

        $fields = $this->getFields();
        $values = [];
        if ($setting = RideDetailPageSetting::with('rideDetailPageSettingDetail')->first()) {
            $detail = optional($setting->rideDetailPageSettingDetail)->first();
            if ($detail) {
                foreach ($fields as $f) { $values[$f] = $detail->{$f} ?? ''; }
            }
        }

        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $values[$field] ?? ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
        foreach ($fields as $f) { $row[$f] = $values[$f] ?? ''; }
        return new Collection([$row]);
    }

    /**
     * All-languages format: one row per field, first column = field name, then one column per language.
     */
    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = $this->getFields();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('rideDetailPageSettingDetail')) {
            foreach ($this->existingData->rideDetailPageSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }

        $rows = [];
        foreach ($fields as $fieldKey) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : '';
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','from_label','to_label','at_label','co_passenger_label','ride_co_passenger_heading','trip_co_passenger_heading','payment_method_label','booking_price_label','booking_method_label','total_seats_label','booking_type_label','cancellation_policy_label','luggage_label','smoking_label','pets_label','seats_left_label','per_seat_label','ride_features_label','ride_seat_label','all_seats_booked_label','ride_canceller_by_driver','ride_completed_text','book_seat_btn_label','book_seats_btn_label','no_seat_available_label','no_ride_found_message','cancel_booking_btn_label','cancel_ride_btn_label','cancel_ride_confirmation','cancel_ride_yes_btn','cancel_ride_no_btn','edit_ride_btn_label','review_label','booking_request_heading','seat_requested_label','request_accept_label','request_reject_label','secured_cash_heading','enter_code_label','mobile_seat_booked_heading','mobile_seat_booked_label','mobile_seat_fare_label','mobile_seat_booking_fee_label','mobile_seat_total_amount_label','vehicle_info_label','driver_info_label','review_driver_info_label','review_passanger_label','driver_chat_with','driver_label','cancellation_policy','passengers_driven_label','driver_age_label','driver_chat_heading','driver_chat_label','driver_chat_button_label','booking_table_heading','passenger_column_label','seat_booked_column_label','total_cost_column_label','booked_on_column_label','status_column_label','booking_requested_status_label','seat_booked_status_label','booking_denied_status_label','actions_column_label','edit_button_actions_label','review_button_label','i_reviewed_label','noon_label','midnight_label','driver_note_label','trip_main_heading','ride_main_heading','discount_label','booking_request_main_heading','passenger_age_label','passenger_gender_label','seat_on_column_label','cancellation_policy_tooltip','cancellation_policy_tooltip_url','pickup_dropoff_info_heading','pickup_label','pickup_at_label','dropoff_label','dropoff_at_label','description_label','verified_email','verified_phone','instant_btn_label','chat_error_message','empty_chat_placeholder'
        ];
    }
}


