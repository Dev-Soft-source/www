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
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('firm_cancellation_label_price_section')->nullable();
            $table->text('firm_discount_label_price_section')->nullable();
            $table->text('firm_your_price_label_price_section')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['firm_cancellation_label_price_section', 'firm_discount_label_price_section', 'firm_your_price_label_price_section']);
        });
    }
};
