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
        Schema::table('billing_address_setting_detail', function (Blueprint $table) {
            $table->text('indicate_field_label')->nullable();
            $table->text('expiry_month_placeholder')->nullable();
            $table->text('cvc_placeholder')->nullable();
            $table->text('card_name_placeholder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_address_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['expiry_month_placeholder', 'cvc_placeholder', 'card_name_placeholder', 'indicate_field_label']);
        });
    }
};
