<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\PostRidePageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PostRidePageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\PostRidePageSetting|null */
    protected $existingData;

    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    protected function detailFields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','post_arrived_again_label','ride_info_heading','from_label','from_placeholder','to_label','to_placeholder','pick_up_label','pick_up_placeholder','drop_off_label','drop_off_placeholder','stop_along_the_way_label','add_stop_btn_label','stop_placeholder','pickup_off_placeholder','delete_stop_text','date_time_label','at_label','recurring_label','recurring_type_label','recurring_trips_label','recurring_trips_placeholder','meeting_drop_off_description_label','meeting_drop_off_description_placeholder','seats_label','seats_middle_label','seats_back_label','vehicle_label','skip_label','add_vehicle_label','existing_label','make_placeholder','model_placeholder','preferences_label','smoking_label','animals_label','features_label','features_option17','booking_label','max_back_seats_label','luggage_label','luggage_checkbox_label1','luggage_checkbox_label1_tooltip','price_payment_heading','price_per_seat_label','payment_methods_label','cancellation_policy_label','anything_to_add_label','anything_to_add_placeholder','disclaimers_label','app_disclaimers_description1','app_disclaimers_description2','app_disclaimers_description3','app_disclaimers_description4','disclaimers_description','agree_terms_label','agree_term_error','price_error_paragraph_1','price_error_paragraph_2','price_error_paragraph_3','price_error_heading','price_error_adjust_btn_label','delete_stop_modal_no_btn','delete_stop_modal_yes_btn','price_warning_heading','price_warning_adjust_btn_label','price_warning_keep_current_btn_label','price_warning_paragraph_1','price_warning_paragraph_2','seats_warning_modal_heading','seats_warning_modal_paragraph','seats_warning_modal_got_it_btn','seats_warning_modal_learn_more_btn','phone_required_modal_heading','phone_required_modal_body_before','phone_required_modal_link_text','phone_required_modal_body_after','phone_required_modal_close_btn','phone_required_modal_phone_btn','alert_need_government_photo_label','alert_need_driver_license_label','submit_button_label','main_heading_update','mobile_agree_terms_label','mobile_term_of_service_label','mobile_agree_terms_and_label','mobile_term_of_use_label','update_button_label','indicates_required_field_text','navbar_icon','repost_ride_btn_label','city_not_in_record','pink_ride_tooltip_only_text','pink_ride_tooltip_female_text','pink_ride_tooltip_complete_profile_text','pink_ride_tooltip_driver_text','pink_ride_tooltip_with_text','pink_ride_tooltip_phone_number_text','pink_ride_tooltip_email_text','pink_ride_tooltip_driver_license_text','pink_ride_tooltip_verified_text','pink_ride_tooltip_select_this_ride_text','extra_care_tooltip_driver_review_text','extra_care_tooltip_greater_age_text','extra_care_tooltip_greater_text','extra_care_tooltip_eligible_text','extra_care_tooltip_complete_profile_text','extra_care_tooltip_verified_text','extra_care_tooltip_driver_license_text','extra_care_tooltip_phone_number_text','extra_care_tooltip_email_text','extra_care_tooltip_and_his_text','select_vehicle_type','vehicle_type_placeholder','seat_text','recurring_type_select_placeholder','recurring_type_daily_label','recurring_type_weekly_label','post_ride_again_main_heading','upcoming_label','completed_label','cancelled_label','cancelled_ride_no_found_message','completed_ride_no_found_message','upcoming_ride_no_found_message','extra_care_tooltip_admin_enable_text','extra_care_tooltip_admin_disable_text','pink_ride_tooltip_admin_enable_text','pink_ride_tooltip_admin_disable_text'
        ];
    }

    protected function subFields(): array
    {
        return ['city_not_fount_contact_text','extra_care_popup_eligible_text','feilds_required_text'];
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        $detail = [
            'name','meta_keywords','meta_description','main_heading','post_arrived_again_label','ride_info_heading','from_label','from_placeholder','to_label','to_placeholder','pick_up_label','pick_up_placeholder','drop_off_label','drop_off_placeholder','stop_along_the_way_label','add_stop_btn_label','stop_placeholder','pickup_off_placeholder','delete_stop_text','date_time_label','at_label','recurring_label','recurring_type_label','recurring_trips_label','recurring_trips_placeholder','meeting_drop_off_description_label','meeting_drop_off_description_placeholder','seats_label','seats_middle_label','seats_back_label','vehicle_label','skip_label','add_vehicle_label','existing_label','make_placeholder','model_placeholder','preferences_label','smoking_label','animals_label','features_label','features_option17','booking_label','max_back_seats_label','luggage_label','luggage_checkbox_label1','luggage_checkbox_label1_tooltip','price_payment_heading','price_per_seat_label','payment_methods_label','cancellation_policy_label','anything_to_add_label','anything_to_add_placeholder','disclaimers_label','app_disclaimers_description1','app_disclaimers_description2','app_disclaimers_description3','app_disclaimers_description4','disclaimers_description','agree_terms_label','agree_term_error','price_error_paragraph_1','price_error_paragraph_2','price_error_paragraph_3','price_error_heading','price_error_adjust_btn_label','delete_stop_modal_no_btn','delete_stop_modal_yes_btn','price_warning_heading','price_warning_adjust_btn_label','price_warning_keep_current_btn_label','price_warning_paragraph_1','price_warning_paragraph_2','seats_warning_modal_heading','seats_warning_modal_paragraph','seats_warning_modal_got_it_btn','seats_warning_modal_learn_more_btn','phone_required_modal_heading','phone_required_modal_body_before','phone_required_modal_link_text','phone_required_modal_body_after','phone_required_modal_close_btn','phone_required_modal_phone_btn','alert_need_government_photo_label','alert_need_driver_license_label','submit_button_label','main_heading_update','mobile_agree_terms_label','mobile_term_of_service_label','mobile_agree_terms_and_label','mobile_term_of_use_label','update_button_label','indicates_required_field_text','navbar_icon','repost_ride_btn_label','city_not_in_record','pink_ride_tooltip_only_text','pink_ride_tooltip_female_text','pink_ride_tooltip_complete_profile_text','pink_ride_tooltip_driver_text','pink_ride_tooltip_with_text','pink_ride_tooltip_phone_number_text','pink_ride_tooltip_email_text','pink_ride_tooltip_driver_license_text','pink_ride_tooltip_verified_text','pink_ride_tooltip_select_this_ride_text','extra_care_tooltip_driver_review_text','extra_care_tooltip_greater_age_text','extra_care_tooltip_greater_text','extra_care_tooltip_eligible_text','extra_care_tooltip_complete_profile_text','extra_care_tooltip_verified_text','extra_care_tooltip_driver_license_text','extra_care_tooltip_phone_number_text','extra_care_tooltip_email_text','extra_care_tooltip_and_his_text','select_vehicle_type','vehicle_type_placeholder','seat_text','recurring_type_select_placeholder','recurring_type_daily_label','recurring_type_weekly_label','post_ride_again_main_heading','upcoming_label','completed_label','cancelled_label','cancelled_ride_no_found_message','completed_ride_no_found_message','upcoming_ride_no_found_message','extra_care_tooltip_admin_enable_text','extra_care_tooltip_admin_disable_text','pink_ride_tooltip_admin_enable_text','pink_ride_tooltip_admin_disable_text'
        ];
        $sub = ['city_not_fount_contact_text','extra_care_popup_eligible_text','feilds_required_text'];
        return array_fill_keys(array_merge($detail, $sub), '');
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') return $this->singleColumnFormat();
        if ($this->format === 'all_languages') return $this->allLanguagesFormat();
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat(): Collection
    {
        $prefill = $this->getPrefillDefaults();
        $fields = array_merge($this->detailFields(), $this->subFields());
        $rows = [];
        foreach ($fields as $field) {
            $rows[] = ['field_name' => $field, 'translation_value' => $prefill[$field] ?? ''];
        }
        return new Collection($rows);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $detailFields = $this->detailFields();
        $subFields = $this->subFields();
        $detailsByLang = [];
        $subDetailsByLang = [];
        if ($this->existingData) {
            if ($this->existingData->relationLoaded('postRidePageSettingDetail')) {
                foreach ($this->existingData->postRidePageSettingDetail as $d) {
                    $detailsByLang[$d->language_id] = $d;
                }
            }
            if ($this->existingData->relationLoaded('postRidePageSettingSubDetail')) {
                foreach ($this->existingData->postRidePageSettingSubDetail as $d) {
                    $subDetailsByLang[$d->language_id] = $d;
                }
            }
        }
        $rows = [];
        foreach (array_merge($detailFields, $subFields) as $fieldKey) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $subDetail = $subDetailsByLang[$lang->id] ?? null;
                if (in_array($fieldKey, $detailFields, true)) {
                    $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : '';
                } else {
                    $value = $subDetail && isset($subDetail->$fieldKey) ? ($subDetail->$fieldKey ?? '') : '';
                }
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    protected function multiColumnFormat(): Collection
    {
        $prefill = $this->getPrefillDefaults();
        $fields = array_merge($this->detailFields(), $this->subFields());
        $row = [];
        foreach ($fields as $field) {
            $row[$field] = $prefill[$field] ?? '';
        }
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        $fields = array_merge($this->detailFields(), $this->subFields());
        return array_map(fn ($f) => ucwords(str_replace('_', ' ', $f)), $fields);
    }

    protected function getPrefillDefaults(): array
    {
        $defaults = [];
        $defaultLang = Language::where('is_default', '1')->first();
        if (!$defaultLang) return $defaults;

        $setting = \App\Models\PostRidePageSetting::first();
        if (!$setting) return $defaults;

        $detail = \App\Models\PostRidePageSettingDetail::where('post_ride_page_setting_id', $setting->id)
            ->where('language_id', $defaultLang->id)->first();
        if ($detail && $detail->navbar_icon) {
            $defaults['navbar_icon'] = $detail->navbar_icon;
        }
        return $defaults;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->format === 'single_column') return ['A' => 45, 'B' => 80];
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 45 : 25;
            }
            return $widths;
        }
        return ['A' => 45, 'B' => 25];
    }
}
