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
        Schema::create('payout_option_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('payout_option_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_opt_setting_id')
                ->constrained()
                ->on('payout_option_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('main_heading')->nullable();
            $table->string('mobile_indicate_required_field_label')->nullable();
            $table->string('bank_detail_heading')->nullable();
            $table->string('paypal_detail_heading')->nullable();
            $table->string('web_bank_transfer_description')->nullable();
            $table->string('web_paypal_transfer_description')->nullable();
            $table->string('web_payout_method_label')->nullable();
            $table->string('web_payout_method_placeholder')->nullable();
            $table->string('bank_name_label')->nullable();
            $table->string('bank_name_placeholder')->nullable();
            $table->string('bank_title_label')->nullable();
            $table->string('bank_title_placeholder')->nullable();
            $table->string('account_number_label')->nullable();
            $table->string('account_number_placeholder')->nullable();
            $table->string('branch_label')->nullable();
            $table->string('branch_placeholder')->nullable();
            $table->string('address_label')->nullable();
            $table->string('address_placeholder')->nullable();
            $table->string(column: 'admin_sent_amount_placeholder')->nullable();
            $table->string(column: 'set_default_checkbox_label')->nullable();
            $table->string(column: 'verify_button_text')->nullable();
            $table->string(column: 'paypal_account_heading')->nullable();
            $table->string(column: 'mobile_paypal_indicate_required_label')->nullable();
            $table->string(column: 'paypal_email_label')->nullable();
            $table->string(column: 'paypal_email_placeholder')->nullable();
            $table->string(column: 'paypal_set_default_checkbox_label')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payout_option_setting_detail');
        Schema::dropIfExists('payout_option_setting');
    }
};
