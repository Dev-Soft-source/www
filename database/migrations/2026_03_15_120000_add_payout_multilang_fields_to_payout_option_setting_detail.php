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
            $table->string('web_interac_transfer_description')->nullable()->after('web_paypal_transfer_description');
            $table->text('wallet_intro_line1')->nullable()->after('main_heading');
            $table->text('wallet_intro_line2')->nullable()->after('wallet_intro_line1');
            $table->string('interac_detail_heading')->nullable()->after('web_interac_transfer_description');
            $table->text('interac_autodeposit_info_paragraph')->nullable();
            $table->string('processing_fee_text')->nullable();
            $table->string('save_payout_method_btn')->nullable();
            $table->text('bank_detail_info_paragraph')->nullable();
            $table->text('bank_funds_note')->nullable();
            $table->text('paypal_detail_info_paragraph')->nullable();
            $table->string('paypal_fee_heading')->nullable();
            $table->string('paypal_fee_proximaride_text')->nullable();
            $table->text('paypal_fee_receiving_text')->nullable();
            $table->text('paypal_fee_example_text')->nullable();
            $table->text('refund_footer_paragraph')->nullable();
            $table->text('interac_autodeposit_label')->nullable();
            $table->text('interac_autodeposit_tooltip')->nullable();
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
                'web_interac_transfer_description',
                'wallet_intro_line1',
                'wallet_intro_line2',
                'interac_detail_heading',
                'interac_autodeposit_info_paragraph',
                'processing_fee_text',
                'save_payout_method_btn',
                'bank_detail_info_paragraph',
                'bank_funds_note',
                'paypal_detail_info_paragraph',
                'paypal_fee_heading',
                'paypal_fee_proximaride_text',
                'paypal_fee_receiving_text',
                'paypal_fee_example_text',
                'refund_footer_paragraph',
                'interac_autodeposit_label',
                'interac_autodeposit_tooltip',
            ]);
        });
    }
};
