<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\MyWalletSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MyWalletSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\MyWalletSetting|null */
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
            'card_heading','passenger_heading','main_heading','driver_heading','balance_heading','passenger_my_ride_heading','passenger_ride_id_label','passenger_my_ride_from_label','passenger_my_ride_date_label','passenger_my_ride_booking_fee_label','passenger_my_ride_fare_label','passenger_my_ride_total_amount_label','passenger_my_reward_heading','passenger_my_reward_description','passenger_my_ride_to_label','passenger_my_reward_points_table_label','passenger_my_reward_reward_table_label','passenger_my_reward_to_label','driver_paid_out_heading','driver_availabe_heading','driver_paid_ride_id_label','driver_paid_from_label','driver_paid_to_label','driver_paid_paid_out_date_label','driver_paid_total_amount_label','driver_available_ride_id_label','driver_available_from_label','driver_available_to_label','driver_available_date_label','driver_available_total_amount_label','driver_pending_heading','driver_pending_data_description','driver_reward_heading','driver_reward_description','driver_reward_points_table_label','driver_reward_reward_table_label','driver_reward_to_label','balance_id_label','balance_amount_label','balance_date_label','balance_buy_more_button_text','no_more_data_message','no_my_ride_message','no_reward_found_message','no_paid_out_message','no_balance_found_message','request_transfer_label','driver_pending_date_label','no_pending_found_message','no_driver_found_message','ride_fare_main_heading','top_up_main_heading','purchase_top_up_label','purchase_top_up_placeholder','purchase_top_up_error','pay_with_label','must_add_amount_toltip','passenger_label','fare_label','booking_fee_label','total_label','credit_card_label','passenger_my_reward_description1','driver_my_reward_description1','claim_my_reward_button_text'
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
        if ($this->existingData && $this->existingData->relationLoaded('myWalletSettingDetail')) {
            foreach ($this->existingData->myWalletSettingDetail as $d) {
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


