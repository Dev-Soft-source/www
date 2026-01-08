<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SiteSettingResource;
use App\Models\SiteSetting;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SiteSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        // $setting = SiteSetting::whereId($id)->first();
        // return $this->apiSuccessResponse(new SiteSettingResource($setting), 'Data Get Successfully!');
        $siteSetting = SiteSetting::query();
        $siteSetting = $siteSetting->first();

        return $this->successResponse($siteSetting ? new SiteSettingResource($siteSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request, SiteSetting $setting)
    {

        $tax_type = $request->filled('tax_type') ? $request->tax_type : null;

        $rules = [
            // 'id' => ['required', 'exists:App\Models\SiteSetting,id'],
            'keywords' => ['required'],
            'description' => ['required'],
            'facebook' => ['required'],
            'instagram' => ['required'],
            'youtube' => ['required'],
            'twitter' => ['required'],
            'frim_discount' => ['required'],
            'ride_completed_hours' => ['required'],
            'destination_hours' => ['required'],
            'booking_price' => ['required'],
            'booking_per' => ['required'],
            'gas_cost' => ['required'],
            'secured_cash_attempt' => ['required'],
            'price_per_km' => ['required'],
            'tax_type' => ['required'],
            'meanu_icon_close_your_account' => ['required'],
            'menu_icon_log_out' => ['required'],
            'menu_icon_coffee_on_the_wall' => ['required'],
            'menu_icon_dispute_policy' => ['required'],
            'menu_icon_contact_proximaride' => ['required'],
            'menu_icon_cancellation_policy' => ['required'],
            'menu_icon_term_of_use' => ['required'],
            'menu_icon_privacy_policy' => ['required'],
            'menu_icon_terms_condition' => ['required'],
            'menu_icon_my_reviews' => ['required'],
            'menu_icon_my_wallet' => ['required'],
            'menu_icon_payment_option' => ['required'],
            'menu_icon_profile_setting' => ['required'],
            'profile_setting_profile_photo' => ['required'],
            'profile_setting_my_vehicle' => ['required'],
            'profile_setting_my_phone_number' => ['required'],
            'profile_setting_password' => ['required'],
            'profile_setting_my_email_address' => ['required'],
            'profile_setting_my_drivers_license' => ['required'],
            'profile_setting_my_student_card' => ['required'],
            'profile_setting_referrals' => ['required'],
            // 'top_menu_add' => ['required'],
            // 'top_menu_notification' => ['required'],
            // 'top_menu_search' => ['required'],
            'booking_cancel_duration' => ['required'],
            'booking_cancel_limit' => ['required'],
            'user_per_day_limit' => ['required'],
            'booking_cancel_duration' => ['required'],
            'ride_post_dead_time' => ['required'],
            'booking_cancel_limit' => ['required'],
            'tax' => $tax_type == "flat_tax" ? 'required' : 'nullable'
        ];
        $this->validate($request, $rules);


        $siteSetting = SiteSetting::first();
        if (!$siteSetting) {
            $setting = SiteSetting::create([
                'id' => '1',
                'site_name' => Config::get('app.name'),
                'keywords' => $request->keywords,
                'description' => $request->description,
                'facebook' => $request->facebook,
                'instagram' => $request->instagram,
                'youtube' => $request->youtube,
                'twitter' => $request->twitter,
                'frim_discount' => $request->frim_discount,
                'ride_completed_hours' => $request->ride_completed_hours,
                'destination_hours' => $request->destination_hours,
                'booking_price' => $request->booking_price,
                'booking_per' => $request->booking_per,
                'gas_cost' => $request->gas_cost,
                'secured_cash_attempt' => $request->secured_cash_attempt,
                'price_per_km' => $request->price_per_km,
                'tax_type' => $request->tax_type,
                'tax' => isset($request->tax) ? $request->tax : NULL,
                'deduct_tax' => $request->deduct_tax,
                'top_menu_add' => $request->top_menu_add,
            'top_menu_search' => $request->top_menu_search,
            'user_per_day_limit' => $request->user_per_day_limit,
            'booking_cancel_duration' => $request->booking_cancel_duration,
            'ride_post_dead_time' => $request->ride_post_dead_time,
            'booking_cancel_limit' => $request->booking_cancel_limit,
            'top_menu_notification' => $request->top_menu_notification,
            'profile_setting_profile_photo' => $request->profile_setting_profile_photo,
            'profile_setting_my_vehicle' => $request->profile_setting_my_vehicle,
            'profile_setting_password' => $request->profile_setting_password,
            'profile_setting_my_phone_number' => $request->profile_setting_my_phone_number,
            'profile_setting_my_email_address' => $request->profile_setting_my_email_address,
            'profile_setting_my_drivers_license' => $request->profile_setting_my_drivers_license,
            'profile_setting_my_student_card' => $request->profile_setting_my_student_card,
            'profile_setting_referrals' => $request->profile_setting_referrals,
            'meanu_icon_close_your_account' => $request->meanu_icon_close_your_account,
            'menu_icon_log_out' => $request->menu_icon_log_out,
            'menu_icon_coffee_on_the_wall' => $request->menu_icon_coffee_on_the_wall,
            'menu_icon_contact_proximaride' => $request->menu_icon_contact_proximaride,
            'menu_icon_dispute_policy' => $request->menu_icon_dispute_policy,
            'menu_icon_cancellation_policy' => $request->menu_icon_cancellation_policy,
            'menu_icon_term_of_use' => $request->menu_icon_term_of_use,
            'menu_icon_privacy_policy' => $request->menu_icon_privacy_policy,
            'menu_icon_terms_condition' => $request->menu_icon_terms_condition,
            'menu_icon_my_reviews' => $request->menu_icon_my_reviews,
            'menu_icon_payment_option' => $request->menu_icon_payment_option,
            'menu_icon_my_wallet' => $request->menu_icon_my_wallet,
            'menu_icon_profile_setting' => $request->menu_icon_profile_setting,
                'booking_fee_give_to_driver' => isset($request->booking_fee_give_to_driver) && $request->booking_fee_give_to_driver == "1"  ? true : false,
                'booking_fee_give_to_student' => isset($request->booking_fee_give_to_student) && $request->booking_fee_give_to_student == "1"  ? true : false
            ]);
        }
        $result = SiteSetting::whereId($request->id)->update([
            'site_name' => $request->site_name,
            'keywords' => $request->keywords,
            'description' => $request->description,
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'twitter' => $request->twitter,
            'frim_discount' => $request->frim_discount,
            'ride_completed_hours' => $request->ride_completed_hours,
            'destination_hours' => $request->destination_hours,
            'booking_price' => $request->booking_price,
            'booking_per' => $request->booking_per,
            'gas_cost' => $request->gas_cost,
            'secured_cash_attempt' => $request->secured_cash_attempt,
            'price_per_km' => $request->price_per_km,
            'tax_type' => $request->tax_type,
            'tax' => isset($request->tax) ? $request->tax : NULL,
            'deduct_tax' => $request->deduct_tax,
            'top_menu_add' => $request->top_menu_add,
            'top_menu_search' => $request->top_menu_search,
            'user_per_day_limit' => $request->user_per_day_limit,
            'booking_cancel_duration' => $request->booking_cancel_duration,
            'ride_post_dead_time' => $request->ride_post_dead_time,
            'booking_cancel_limit' => $request->booking_cancel_limit,
            'top_menu_notification' => $request->top_menu_notification,
            'profile_setting_profile_photo' => $request->profile_setting_profile_photo,
            'profile_setting_my_vehicle' => $request->profile_setting_my_vehicle,
            'profile_setting_password' => $request->profile_setting_password,
            'profile_setting_my_phone_number' => $request->profile_setting_my_phone_number,
            'profile_setting_my_email_address' => $request->profile_setting_my_email_address,
            'profile_setting_my_drivers_license' => $request->profile_setting_my_drivers_license,
            'profile_setting_my_student_card' => $request->profile_setting_my_student_card,
            'profile_setting_referrals' => $request->profile_setting_referrals,
            'meanu_icon_close_your_account' => $request->meanu_icon_close_your_account,
            'menu_icon_log_out' => $request->menu_icon_log_out,
            'menu_icon_coffee_on_the_wall' => $request->menu_icon_coffee_on_the_wall,
            'menu_icon_contact_proximaride' => $request->menu_icon_contact_proximaride,
            'menu_icon_dispute_policy' => $request->menu_icon_dispute_policy,
            'menu_icon_cancellation_policy' => $request->menu_icon_cancellation_policy,
            'menu_icon_term_of_use' => $request->menu_icon_term_of_use,
            'menu_icon_privacy_policy' => $request->menu_icon_privacy_policy,
            'menu_icon_terms_condition' => $request->menu_icon_terms_condition,
            'menu_icon_my_reviews' => $request->menu_icon_my_reviews,
            'menu_icon_payment_option' => $request->menu_icon_payment_option,
            'menu_icon_my_wallet' => $request->menu_icon_my_wallet,
            'menu_icon_profile_setting' => $request->menu_icon_profile_setting,
            'booking_fee_give_to_driver' => isset($request->booking_fee_give_to_driver) && $request->booking_fee_give_to_driver == "1"  ? true : false,
            'booking_fee_give_to_student' => isset($request->booking_fee_give_to_student) && $request->booking_fee_give_to_student == "1"  ? true : false
        ]);

        if ($result || $setting) {
            return $this->apiSuccessResponse(new SiteSettingResource($setting), 'Settings have been updated successfully.');
        }
        return $this->errorResponse();
    }
}
