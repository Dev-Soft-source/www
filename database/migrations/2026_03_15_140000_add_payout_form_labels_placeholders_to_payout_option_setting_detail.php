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
            $table->string('interac_email_label')->nullable()->after('interac_autodeposit_tooltip');
            $table->string('interac_email_confirm_label')->nullable()->after('interac_email_label');
            $table->string('interac_email_placeholder')->nullable()->after('interac_email_confirm_label');
            $table->string('interac_email_confirm_placeholder')->nullable()->after('interac_email_placeholder');
            $table->string('paypal_email_confirm_label')->nullable()->after('paypal_email_placeholder');
            $table->string('paypal_email_confirm_placeholder')->nullable()->after('paypal_email_confirm_label');
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
            $table->dropColumn([
                'interac_email_label',
                'interac_email_confirm_label',
                'interac_email_placeholder',
                'interac_email_confirm_placeholder',
                'paypal_email_confirm_label',
                'paypal_email_confirm_placeholder',
            ]);
        });
    }
};
