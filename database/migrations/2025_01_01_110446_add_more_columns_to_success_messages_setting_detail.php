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
            $table->text('license_delete_message')->nullable();
            $table->text('post_ride_update_message')->nullable();
            $table->text('reply_already_exist_message')->nullable();
            $table->text('closed_account_success_message')->nullable();
            $table->text('booking_not_update_message')->nullable();
            $table->text('bank_detail_update_message')->nullable();
            $table->text('account_closed_message')->nullable();
            $table->text('seat_hold_success_message')->nullable();
            $table->text('seat_booked_message')->nullable();
            $table->text('request_accept_message')->nullable();
            $table->text('request_expired_message')->nullable();
            $table->text('email_already_exist_message')->nullable();
            $table->text('admin_sent_verify_amount_message')->nullable();
            $table->text('bank_already_verified_message')->nullable();
            $table->text('bank_verified_message')->nullable();
            $table->text('verify_amount_not_match_message')->nullable();
            $table->text('card_expiry_message')->nullable();
            $table->text('topup_balance_success_message')->nullable();
            $table->text('overlap_ride_message')->nullable();
            $table->text('password_reset_success_message')->nullable();
            $table->text('email_not_found_message')->nullable();
            $table->text('current_email_not_match')->nullable();
            $table->text('general_error_message')->nullable();
            $table->text('removed_passenger_message')->nullable();
            $table->text('verfification_code_sent_message')->nullable();
            $table->text('phone_set_default_message')->nullable();
            $table->text('password_token_invalid_message')->nullable();
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
            //
        });
    }
};
