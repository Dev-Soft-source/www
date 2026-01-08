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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('pink_ride_tooltip_only_text')->nullable();
            $table->text('pink_ride_tooltip_female_text')->nullable();
            $table->text('pink_ride_tooltip_driver_text')->nullable();
            $table->text('pink_ride_tooltip_with_text')->nullable();
            $table->text('pink_ride_tooltip_phone_number_text')->nullable();
            $table->text('pink_ride_tooltip_email_text')->nullable();
            $table->text('pink_ride_tooltip_driver_license_text')->nullable();
            $table->text('pink_ride_tooltip_verified_text')->nullable();
            $table->text('pink_ride_tooltip_select_this_ride_text')->nullable();

            $table->text('extra_care_tooltip_greater_text')->nullable();
            $table->text('extra_care_tooltip_eligible_text')->nullable();
            $table->text('extra_care_tooltip_verified_text')->nullable();
            $table->text('extra_care_tooltip_driver_license_text')->nullable();
            $table->text('extra_care_tooltip_phone_number_text')->nullable();
            $table->text('extra_care_tooltip_email_text')->nullable();
            $table->text('extra_care_tooltip_and_his_text')->nullable();
            $table->text('extra_care_tooltip_greater_age_text')->nullable();
            $table->text('extra_care_tooltip_driver_review_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'pink_ride_tooltip_only_text',
                'pink_ride_tooltip_female_text',
                'pink_ride_tooltip_driver_text',
                'pink_ride_tooltip_with_text',
                'pink_ride_tooltip_phone_number_text',
                'pink_ride_tooltip_email_text',
                'pink_ride_tooltip_driver_license_text',
                'pink_ride_tooltip_verified_text',
                'pink_ride_tooltip_select_this_ride_text',
                'extra_care_tooltip_greater_text',
                'extra_care_tooltip_eligible_text',
                'extra_care_tooltip_verified_text',
                'extra_care_tooltip_driver_license_text',
                'extra_care_tooltip_phone_number_text',
                'extra_care_tooltip_email_text',
                'extra_care_tooltip_and_his_text',
                'extra_care_tooltip_driver_review_text',
                'extra_care_tooltip_greater_age_text',
            ]);
        });
    }
};
