<?php

namespace App\Exports;

use App\Models\FindRidePageSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FindRidePageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\FindRidePageSetting|null */
    protected $existingData;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages
     * @param \App\Models\FindRidePageSetting|null $existingData - with findRidePageSettingDetail loaded
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        $fields = [
            'name', 'meta_keywords', 'meta_description', 'main_heading', 'pink_ride_page_heading', 'extra_care_ride_page_label', 'pink_ride_page_label', 'pink_ride_description', 'pink_ride_page_faq_heading', 'search_results_pink_ride_label', 'search_results_extra_care_ride_label', 'more_rides_pink_ride_label', 'to_pink_ride_label', 'imp_pink_ride_label', 'imp_extra_care_ride_label', 'navbar_icon', 'from_field_icon', 'swap_field_icon', 'to_field_icon', 'date_field_icon', 'search_field_icon', 'search_section_from_placeholder', 'search_section_to_placeholder', 'search_section_date_placeholder', 'search_section_required_error', 'search_section_keyword_label', 'search_section_keyword_placeholder', 'search_section_button_label', 'search_section_recent_searches', 'card_section_from_label', 'card_section_to_label', 'card_section_at_label', 'card_section_seats_left', 'card_section_per_seat', 'heading_ride_card_section', 'card_section_booked', 'card_section_seats', 'card_section_booking_fee', 'card_section_seats_fee', 'card_section_amount', 'card_section_driver', 'card_section_age', 'card_section_driven', 'card_section_passengers', 'card_section_review', 'card_section_completed', 'trips_card_section_seat_booked', 'trips_card_section_seat_available', 'trips_card_section_review_driver', 'firm_cancellation_tooltip', 'filter_section_heading', 'filter1_driver_heading', 'driver_age_label', 'driver_age_placeholder', 'driver_rating_label', 'driver_rating_placeholder', 'driver_phone_access_label', 'driver_know_label', 'driver_know_placeholder', 'filter2_passengers_heading', 'passengers_rating_label', 'passengers_rating_placeholder', 'filter3_payment_methods_heading', 'apply_button_label', 'clear_button_label', 'payment_methods_label', 'payment_methods_option1', 'filter4_vehicle_heading', 'vehicle_type_label', 'vehicle_type_placeholder', 'ride_features_option17', 'luggage_label', 'luggage_placeholder', 'smoking_label', 'pets_allowed_label', 'card_section_cancelled', 'search_filter_all_label', 'search_filter_select_vehicle_label', 'card_section_not_live', 'card_section_booking_request', 'trips_card_section_reviewed', 'card_section_no_review', 'search_result_load_more_btn', 'search_result_no_more_data_message', 'search_result_no_found_message', 'search_result_label', 'filter_what_label', 'search_and_above_label', 'ride_preferences_label', 'search_section_pink_ride_label', 'search_section_extra_care_label', 'filter_search_btn_label', 'filter_close_btn_label', 'hide_ride_popup_heading', 'hide_ride_popup_text', 'hide_ride_popup_confirm_button', 'hide_ride_popup_take_me_back_button',
        ];
        return array_fill_keys($fields, '');
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        $iconDefaults = $this->getDefaultIconValues();
        $data = [];
        foreach ($fields as $field => $default) {
            $value = $iconDefaults[$field] ?? $default;
            $data[] = ['field_name' => $field, 'translation_value' => $value];
        }
        return new Collection($data);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('findRidePageSettingDetail')) {
            foreach ($this->existingData->findRidePageSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }
        $rows = [];
        foreach ($fields as $fieldKey => $defaultValue) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : $defaultValue;
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    protected function multiColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        $iconDefaults = $this->getDefaultIconValues();
        $row = $fields;
        foreach ($iconDefaults as $k => $v) {
            $row[$k] = $v ?? '';
        }
        return new Collection([$row]);
    }

    protected function getDefaultIconValues(): array
    {
        $defaultLang = Language::where('is_default', '1')->first();
        $langId = $defaultLang->id ?? 1;
        $detail = FindRidePageSettingDetail::where('language_id', $langId)->first();
        if (!$detail) {
            return [];
        }
        $iconFields = ['navbar_icon', 'from_field_icon', 'swap_field_icon', 'to_field_icon', 'date_field_icon', 'search_field_icon'];
        $defaults = [];
        foreach ($iconFields as $f) {
            $defaults[$f] = $detail->$f ?? '';
        }
        return $defaults;
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
        return array_map(fn ($f) => ucwords(str_replace('_', ' ', $f)), array_keys(static::getTranslatableFieldsWithDefaults()));
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
        if ($this->format === 'single_column') {
            return ['A' => 35, 'B' => 80];
        }
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 35 : 25;
            }
            return $widths;
        }
        return ['A' => 35, 'B' => 25];
    }
}
