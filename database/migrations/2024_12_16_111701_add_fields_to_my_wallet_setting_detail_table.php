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
        Schema::table('my_wallet_setting_detail', function (Blueprint $table) {
            $table->text('top_up_main_heading')->nullable();
            $table->text('purchase_top_up_label')->nullable();
            $table->text('purchase_top_up_placeholder')->nullable();
            $table->text('pay_with_label')->nullable();
            $table->text('must_add_amount_toltip')->nullable();
            $table->text('ride_fare_main_heading')->nullable();
            $table->text('passenger_label')->nullable();
            $table->text('fare_label')->nullable();
            $table->text('booking_fee_label')->nullable();
            $table->text('total_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_wallet_setting_detail', function (Blueprint $table) {

           $table->dropColumn('total_label');
           $table->dropColumn('booking_fee_label');
           $table->dropColumn('fare_label');
           $table->dropColumn('passenger_label');
           $table->dropColumn('ride_fare_main_heading');
           $table->dropColumn('must_add_amount_toltip');
           $table->dropColumn('pay_with_label');
           $table->dropColumn('purchase_top_up_placeholder');
           $table->dropColumn('purchase_top_up_label');
           $table->dropColumn('top_up_main_heading');

        });
    }
};
