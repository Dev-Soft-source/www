<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyWalletSettingDetailResource extends JsonResource
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
            'wallet_setting_id' => $this->wallet_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'passenger_heading' => $this->passenger_heading,
            'card_heading' => $this->card_heading,
            'driver_heading' => $this->driver_heading,
            'balance_heading' => $this->balance_heading,
            'passenger_my_ride_heading' => $this->passenger_my_ride_heading,
            'passenger_ride_id_label' => $this->passenger_ride_id_label,
            'passenger_my_ride_from_label' => $this->passenger_my_ride_from_label,
            'passenger_my_ride_to_label' => $this->passenger_my_ride_to_label,
            'passenger_my_ride_date_label' => $this->passenger_my_ride_date_label,
            'passenger_my_ride_booking_fee_label' => $this->passenger_my_ride_booking_fee_label,
            'passenger_my_ride_fare_label' => $this->passenger_my_ride_fare_label,
            'passenger_my_ride_total_amount_label' => $this->passenger_my_ride_total_amount_label,
            'passenger_my_reward_heading' => $this->passenger_my_reward_heading,
            'passenger_my_reward_description' => $this->passenger_my_reward_description,
            'passenger_my_reward_points_table_label' => $this->passenger_my_reward_points_table_label,
            'passenger_my_reward_reward_table_label' => $this->passenger_my_reward_reward_table_label,
            'passenger_my_reward_to_label' => $this->passenger_my_reward_to_label,
            'driver_paid_out_heading' => $this->driver_paid_out_heading,
            'driver_availabe_heading' => $this->driver_availabe_heading,
            'driver_paid_ride_id_label' => $this->driver_paid_ride_id_label,
            'driver_paid_from_label' => $this->driver_paid_from_label,
            'driver_paid_to_label' => $this->driver_paid_to_label,
            'driver_paid_paid_out_date_label' => $this->driver_paid_paid_out_date_label,
            'driver_paid_total_amount_label' => $this->driver_paid_total_amount_label,
            'driver_available_ride_id_label' => $this->driver_available_ride_id_label,
            'driver_available_from_label' => $this->driver_available_from_label,
            'driver_available_to_label' => $this->driver_available_to_label,
            'driver_available_date_label' => $this->driver_available_date_label,
            'driver_available_total_amount_label' => $this->driver_available_total_amount_label,
            'driver_pending_heading' => $this->driver_pending_heading,
            'driver_pending_data_description' => $this->driver_pending_data_description,
            'driver_reward_heading' => $this->driver_reward_heading,
            'driver_reward_description' => $this->driver_reward_description,
            'driver_reward_points_table_label' => $this->driver_reward_points_table_label,
            'driver_reward_reward_table_label' => $this->driver_reward_reward_table_label,
            'driver_reward_to_label' => $this->driver_reward_to_label,
            'balance_id_label' => $this->balance_id_label,
            'balance_amount_label' => $this->balance_amount_label,
            'balance_date_label' => $this->balance_date_label,
            'no_balance_found_message' => $this->no_balance_found_message,
            'no_driver_found_message' => $this->no_driver_found_message,
            'no_pending_found_message' => $this->no_pending_found_message,
            'driver_pending_date_label' => $this->driver_pending_date_label,
            'request_transfer_label' => $this->request_transfer_label,
            'no_paid_out_message' => $this->no_paid_out_message,
            'no_reward_found_message' => $this->no_reward_found_message,
            'no_my_ride_message' => $this->no_my_ride_message,
            'no_more_data_message' => $this->no_more_data_message,
            'balance_buy_more_button_text' => $this->balance_buy_more_button_text,
            'total_label' => $this->total_label,
            'booking_fee_label' => $this->booking_fee_label,
            'fare_label' => $this->fare_label,
            'passenger_label' => $this->passenger_label,
            'must_add_amount_toltip' => $this->must_add_amount_toltip,
            'pay_with_label' => $this->pay_with_label,
            'purchase_top_up_placeholder' => $this->purchase_top_up_placeholder,
            'purchase_top_up_error' => $this->purchase_top_up_error,
            'purchase_top_up_label' => $this->purchase_top_up_label,
            'top_up_main_heading' => $this->top_up_main_heading,
            'ride_fare_main_heading' => $this->ride_fare_main_heading,
            'credit_card_label' => $this->credit_card_label,
            'passenger_my_reward_description1' => $this->passenger_my_reward_description1,
            'driver_my_reward_description1' => $this->driver_my_reward_description1,
            'claim_my_reward_button_text' => $this->claim_my_reward_button_text,
            'my_wallet_setting' => new MyWalletSettingResource($this->whenLoaded('myWalletSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
