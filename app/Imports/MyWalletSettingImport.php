<?php

namespace App\Imports;

use App\Models\MyWalletSetting;
use App\Models\MyWalletSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyWalletSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'card_heading','passenger_heading','main_heading','driver_heading','balance_heading','passenger_my_ride_heading','passenger_ride_id_label','passenger_my_ride_from_label','passenger_my_ride_date_label','passenger_my_ride_booking_fee_label','passenger_my_ride_fare_label','passenger_my_ride_total_amount_label','passenger_my_reward_heading','passenger_my_reward_description','passenger_my_ride_to_label','passenger_my_reward_points_table_label','passenger_my_reward_reward_table_label','passenger_my_reward_to_label','driver_paid_out_heading','driver_availabe_heading','driver_paid_ride_id_label','driver_paid_from_label','driver_paid_to_label','driver_paid_paid_out_date_label','driver_paid_total_amount_label','driver_available_ride_id_label','driver_available_from_label','driver_available_to_label','driver_available_date_label','driver_available_total_amount_label','driver_pending_heading','driver_pending_data_description','driver_reward_heading','driver_reward_description','driver_reward_points_table_label','driver_reward_reward_table_label','driver_reward_to_label','balance_id_label','balance_amount_label','balance_date_label','balance_buy_more_button_text','no_more_data_message','no_my_ride_message','no_reward_found_message','no_paid_out_message','no_balance_found_message','request_transfer_label','driver_pending_date_label','no_pending_found_message','no_driver_found_message','ride_fare_main_heading','top_up_main_heading','purchase_top_up_label','purchase_top_up_placeholder','purchase_top_up_error','pay_with_label','must_add_amount_toltip','passenger_label','fare_label','booking_fee_label','total_label','credit_card_label','passenger_my_reward_description1','driver_my_reward_description1','claim_my_reward_button_text'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyWalletSetting::first() ?? MyWalletSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingleColumn = isset($keys[0]) && (in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            $data = [];
            foreach ($rows as $row) {
                $name = strtolower(trim($row['field_name'] ?? ''));
                if (!$name || !in_array($name, $this->fieldsList())) continue;
                $data[$name] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($setting, $data);
        } else {
            $this->applyData($setting, $firstRow->toArray());
        }
    }

    protected function applyData($setting, array $data): void
    {
        $payload = [
            'wallet_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) { $payload[$f] = $data[$f] ?? null; }

        MyWalletSettingDetail::updateOrCreate(
            [
                'wallet_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $payload
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'card_heading' => 'required|string',
            'passenger_heading' => 'required|string',
            'main_heading' => 'required|string',
            'driver_heading' => 'required|string',
            'balance_heading' => 'required|string',
            'passenger_my_ride_heading' => 'required|string',
            'passenger_my_ride_from_label' => 'required|string',
            'passenger_my_ride_date_label' => 'required|string',
            'passenger_my_ride_booking_fee_label' => 'required|string',
            'passenger_my_ride_fare_label' => 'required|string',
            'passenger_my_ride_total_amount_label' => 'required|string',
            'passenger_my_reward_heading' => 'required|string',
            'passenger_my_reward_description' => 'required|string',
            'passenger_my_ride_to_label' => 'required|string',
            'driver_paid_out_heading' => 'required|string',
            'driver_availabe_heading' => 'required|string',
            'driver_paid_ride_id_label' => 'required|string',
            'driver_paid_from_label' => 'required|string',
            'driver_paid_to_label' => 'required|string',
            'driver_paid_paid_out_date_label' => 'required|string',
            'driver_paid_total_amount_label' => 'required|string',
            'balance_id_label' => 'required|string',
            'balance_amount_label' => 'required|string',
            'balance_date_label' => 'required|string',
            'balance_buy_more_button_text' => 'required|string',
        ];
    }
}


