<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'site_name',
        'keywords',
        'description',
        'facebook',
        'instagram',
        'youtube',
        'twitter',
        'booking_price',
        'booking_per',
        'ride_completed_hours',
        'destination_hours',
        'gas_cost',
        'secured_cash_attempt',
        'price_per_km',
        'tax_type',
        'tax',
        'deduct_tax',
        'top_menu_notification',
        'top_menu_search',
        'top_menu_add',
        'profile_setting_profile_photo',
        'profile_setting_my_vehicle',
        'profile_setting_password',
        'profile_setting_my_phone_number',
        'profile_setting_my_email_address',
        'profile_setting_my_drivers_license',
        'profile_setting_my_student_card',
        'profile_setting_referrals',
        'menu_icon_profile_setting',
        'menu_icon_my_wallet',
        'menu_icon_payment_option',
        'menu_icon_my_reviews',
        'menu_icon_terms_condition',
        'menu_icon_privacy_policy',
        'menu_icon_term_of_use',
        'menu_icon_cancellation_policy',
        'menu_icon_dispute_policy',
        'menu_icon_contact_proximaride',
        'menu_icon_coffee_on_the_wall',
        'menu_icon_log_out',
        'meanu_icon_close_your_account'
    ];
}
