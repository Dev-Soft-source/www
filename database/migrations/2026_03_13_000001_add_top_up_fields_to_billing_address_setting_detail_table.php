<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('billing_address_setting_detail', function (Blueprint $table) {
            $table->text('buy_btn_text')->nullable()->after('save_button_text');
            $table->text('top_up_my_balance_head')->nullable()->after('buy_btn_text');
            $table->text('purchase_amount_label')->nullable()->after('top_up_my_balance_head');
            $table->text('purchase_amount_placeholder')->nullable()->after('purchase_amount_label');
        });
    }

    public function down()
    {
        Schema::table('billing_address_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'buy_btn_text',
                'top_up_my_balance_head',
                'purchase_amount_label',
                'purchase_amount_placeholder',
            ]);
        });
    }
};
