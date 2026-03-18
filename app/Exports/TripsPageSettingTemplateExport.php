<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\TripsPageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TripsPageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\TripsPageSetting|null */
    protected $existingData;

    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        $fields = [
            'name','meta_keywords','meta_description','passenger_trips_heading','driver_rides_heading','upcoming_label','no_upcoming_trips_label','no_upcoming_rides_label','completed_label','no_completed_trips_label','no_completed_rides_label','cancelled_label','no_cancelled_trips_label','no_cancelled_rides_label','timeliness_label','safety_label','respect_and_courtesy_label','personal_hygiene_label','overall_attitude_label','communication_label','comfort_label','conscious_passenger_wellness_label','condition_label','review_criteria_label','main_heading','average_label','load_more_trips_label','no_more_data_message','load_more_rides_label','review_passengers_review_label','review_passengers_i_review_label','review_passengers_heading','passenger_cancel_ride_btn_label','booking_cancel_btn_label','cancel_booking_trip_placeholder','cancel_all_feilds_are_required','cancel_ride_label','cancel_ride_placeholder','cancel_seat_label','number_of_seat_booked','cancel_booking_heading','cancel_booking_main_heading','cancel_ride_setting','tell_passenger_why_label','tell_passenger_why_placeholder','confirm_cancel_ride','remove_from_this_ride_message','remove_passenger_and_block_message','remove_day_label','remove_day_error','driver_remove_reason_placeholder','passenger_remove_reason_placeholder','passenger_review_heading','driver_review_heading','passenger_review_placeholder','driver_review_placeholder','review_submit_btn_label','remove_passenger_heading','remove_passenger_text','block_temporarily_label','block_permanently_label','remove_day_placeholder','driver_remove_reason_label','driver_remove_reason_error','passenger_remove_reason_label','passenger_remove_reason_error','passenger_cancel_sure_message','cancel_message_title','cancel_booking_confirm_message','booking_cancel_btn_yes_label','booking_cancel_btn_no_label','cancel_booking_confirm_firm_message','cancel_ride_confirm_decision_title','cancel_ride_confirm_ok_btn_text','cancel_booking_confirm_48_hour_message','cancel_booking_confirm_12_to_48_hour_message','cancel_booking_confirm_less_12_hour_message'
        ];
        return array_fill_keys($fields, '');
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') return $this->singleColumnFormat();
        if ($this->format === 'all_languages') return $this->allLanguagesFormat();
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        $data = [];
        foreach ($fields as $field => $default) {
            $data[] = ['field_name' => $field, 'translation_value' => $default];
        }
        return new Collection($data);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('tripsPageSettingDetail')) {
            foreach ($this->existingData->tripsPageSettingDetail as $d) {
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
        return new Collection([$fields]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
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
        if ($this->format === 'single_column') return ['A' => 40, 'B' => 80];
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 40 : 25;
            }
            return $widths;
        }
        return ['A' => 40, 'B' => 25];
    }
}


