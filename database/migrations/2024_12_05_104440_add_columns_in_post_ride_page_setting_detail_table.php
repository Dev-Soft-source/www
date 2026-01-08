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
            $table->text('main_heading_update')->after('main_heading')->nullable();
            $table->text('cancellation_policy_label1_tooltip')->after('cancellation_policy_label1')->nullable();
            $table->text('cancellation_policy_label2_tooltip')->after('cancellation_policy_label2')->nullable();
            $table->text('mobile_agree_terms_label')->after('agree_terms_label')->nullable();
            $table->text('mobile_term_of_service_label')->after('mobile_agree_terms_label')->nullable();
            $table->text('mobile_agree_terms_and_label')->after('mobile_term_of_service_label')->nullable();
            $table->text('mobile_term_of_use_label')->after('mobile_agree_terms_and_label')->nullable();
            $table->text('update_button_label')->after('submit_button_label')->nullable();
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
            $table->dropColumn(['update_button_label', 'mobile_term_of_use_label', 'mobile_agree_terms_and_label',
                'mobile_term_of_service_label', 'mobile_agree_terms_label', 'cancellation_policy_label2_tooltip',
                'cancellation_policy_label1_tooltip', 'main_heading_update'
            ]);
        });
    }
};
