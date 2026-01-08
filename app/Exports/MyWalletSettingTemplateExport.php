<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MyWalletSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            'card_heading','passenger_heading','main_heading','driver_heading','balance_heading','passenger_my_ride_heading','passenger_ride_id_label','passenger_my_ride_from_label','passenger_my_ride_date_label','passenger_my_ride_booking_fee_label','passenger_my_ride_fare_label','passenger_my_ride_total_amount_label','passenger_my_reward_heading','passenger_my_reward_description','passenger_my_ride_to_label','passenger_my_reward_points_table_label','passenger_my_reward_reward_table_label','passenger_my_reward_to_label','driver_paid_out_heading','driver_availabe_heading','driver_paid_ride_id_label','driver_paid_from_label','driver_paid_to_label','driver_paid_paid_out_date_label','driver_paid_total_amount_label','driver_available_ride_id_label','driver_available_from_label','driver_available_to_label','driver_available_date_label','driver_available_total_amount_label','driver_pending_heading','driver_pending_data_description','driver_reward_heading','driver_reward_description','driver_reward_points_table_label','driver_reward_reward_table_label','driver_reward_to_label','balance_id_label','balance_amount_label','balance_date_label','balance_buy_more_button_text','no_more_data_message','no_my_ride_message','no_reward_found_message','no_paid_out_message','no_balance_found_message','request_transfer_label','driver_pending_date_label','no_pending_found_message','no_driver_found_message','ride_fare_main_heading','top_up_main_heading','purchase_top_up_label','purchase_top_up_placeholder','purchase_top_up_error','pay_with_label','must_add_amount_toltip','passenger_label','fare_label','booking_fee_label','total_label','credit_card_label','passenger_my_reward_description1','driver_my_reward_description1','claim_my_reward_button_text'
        ];
    }
}


