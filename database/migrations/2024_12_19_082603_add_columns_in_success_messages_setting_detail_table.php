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
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->string('past_date_message')->nullable();
            $table->string('enter_code_message')->nullable();
            $table->string('url_not_allowed_message')->nullable();
            $table->string('email_not_allowed_message')->nullable();
            $table->string('phone_number_not_allowed_message')->nullable();
            $table->string('need_to_select_card_message')->nullable();
            $table->string('paypal_not_completed_message')->nullable();
            $table->string('search_result_clear_message')->nullable();
            $table->string('delete_card_message')->nullable();
            $table->string('withdraw_message')->nullable();
            $table->string('delete_vehicle_message')->nullable();
            $table->string('remove_driver_license_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['remove_driver_license_message', 'delete_vehicle_message', 'withdraw_message', 'delete_card_message', 'search_result_clear_message', 'paypal_not_completed_message', 'need_to_select_card_message', 'phone_number_not_allowed_message', 'email_not_allowed_message', 'url_not_allowed_message', 'enter_code_message', 'past_date_message']);
        });
    }
};
