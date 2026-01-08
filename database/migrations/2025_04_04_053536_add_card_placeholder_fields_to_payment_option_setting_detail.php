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
        Schema::table('payment_option_setting_detail', function (Blueprint $table) {
            $table->text('card_number_placeholder')->nullable();
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
        Schema::table('payment_option_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['expiry_month_placeholder', 'card_number_placeholder', 'cvc_placeholder', 'card_name_placeholder']);
        });
    }
};
