<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('top_menu_notification')->nullable();
            $table->string('top_menu_search')->nullable();
            $table->string('top_menu_add')->nullable();
            $table->string('profile_setting_profile_photo')->nullable();
            $table->string('profile_setting_my_vehicle')->nullable();
            $table->string('profile_setting_password')->nullable();
            $table->string('profile_setting_my_phone_number')->nullable();
            $table->string('profile_setting_my_email_address')->nullable();
            $table->string('profile_setting_my_drivers_license')->nullable();
            $table->string('profile_setting_my_student_card')->nullable();
            $table->string('profile_setting_referrals')->nullable();
            $table->string('menu_icon_profile_setting')->nullable();
            $table->string('menu_icon_my_wallet')->nullable();
            $table->string('menu_icon_payment_option')->nullable();
            $table->string('menu_icon_my_reviews')->nullable();
            $table->string('menu_icon_terms_condition')->nullable();
            $table->string('menu_icon_privacy_policy')->nullable();
            $table->string('menu_icon_term_of_use')->nullable();
            $table->string('menu_icon_cancellation_policy')->nullable();
            $table->string('menu_icon_dispute_policy')->nullable();
            $table->string('menu_icon_contact_proximaride')->nullable();
            $table->string('menu_icon_coffee_on_the_wall')->nullable();
            $table->string('menu_icon_log_out')->nullable();
            $table->string('meanu_icon_close_your_account')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
