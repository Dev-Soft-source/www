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
        Schema::table('payout_option_setting_detail', function (Blueprint $table) {
            $table->text('bank_error')->nullable();
            $table->text('bank_title_error')->nullable();
            $table->text('acc_no_error')->nullable();
            $table->text('branch_error')->nullable();
            $table->text('address_error')->nullable();
            $table->text('paypal_email_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payout_option_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['paypal_email_error', 'address_error', 'branch_error', 'acc_no_error', 'bank_title_error',
                'bank_error'
            ]);
        });
    }
};
