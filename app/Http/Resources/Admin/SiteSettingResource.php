<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'site_name' => $this->site_name,
            'keywords' => $this->keywords,
            'description' => $this->description,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'youtube' => $this->youtube,
            'twitter' => $this->twitter,
            'frim_discount' => $this->frim_discount,
            'ride_completed_hours' => $this->ride_completed_hours,
            'destination_hours' => $this->destination_hours,
            'booking_price' => $this->booking_price,
            'booking_per' => $this->booking_per,
            'gas_cost' => $this->gas_cost,
            'secured_cash_attempt' => $this->secured_cash_attempt,
            'price_per_km' => $this->price_per_km,
            'deduct_tax' => $this->deduct_tax,
            'tax' => $this->tax,
            'tax_type' => $this->tax_type,
            'booking_fee_give_to_driver' => $this->booking_fee_give_to_driver,
            'booking_fee_give_to_student' => $this->booking_fee_give_to_student,
            'top_menu_add' => $this->top_menu_add,
            'top_menu_search' => $this->top_menu_search,
            'top_menu_notification' => $this->top_menu_notification,
            'profile_setting_profile_photo' => $this->profile_setting_profile_photo,
            'profile_setting_my_vehicle' => $this->profile_setting_my_vehicle,
            'profile_setting_password' => $this->profile_setting_password,
            'profile_setting_my_phone_number' => $this->profile_setting_my_phone_number,
            'profile_setting_my_email_address' => $this->profile_setting_my_email_address,
            'profile_setting_my_drivers_license' => $this->profile_setting_my_drivers_license,
            'profile_setting_my_student_card' => $this->profile_setting_my_student_card,
            'profile_setting_referrals' => $this->profile_setting_referrals,
            'meanu_icon_close_your_account' => $this->meanu_icon_close_your_account,
            'menu_icon_log_out' => $this->menu_icon_log_out,
            'menu_icon_coffee_on_the_wall' => $this->menu_icon_coffee_on_the_wall,
            'menu_icon_contact_proximaride' => $this->menu_icon_contact_proximaride,
            'menu_icon_dispute_policy' => $this->menu_icon_dispute_policy,
            'menu_icon_cancellation_policy' => $this->menu_icon_cancellation_policy,
            'menu_icon_term_of_use' => $this->menu_icon_term_of_use,
            'menu_icon_privacy_policy' => $this->menu_icon_privacy_policy,
            'menu_icon_terms_condition' => $this->menu_icon_terms_condition,
            'menu_icon_my_reviews' => $this->menu_icon_my_reviews,
            'menu_icon_payment_option' => $this->menu_icon_payment_option,
            'menu_icon_my_wallet' => $this->menu_icon_my_wallet,
            'menu_icon_profile_setting' => $this->menu_icon_profile_setting,
            'user_per_day_limit' => $this->user_per_day_limit,
            'booking_cancel_duration' => $this->booking_cancel_duration,
            'ride_post_dead_time' => $this->ride_post_dead_time,
            'booking_cancel_limit' => $this->booking_cancel_limit,

        ];
    }
}
