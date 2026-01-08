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
            $table->string('no_more_data_message')->nullable();
            $table->string('no_my_ride_message')->nullable();
            $table->string('no_reward_found_message')->nullable();
            $table->string('no_paid_out_message')->nullable();
            $table->string('no_balance_found_message')->nullable();
            $table->string('request_transfer_label')->nullable();
            $table->string('driver_pending_date_label')->nullable();
            $table->string('no_pending_found_message')->nullable();
            $table->string('no_driver_found_message')->nullable();
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

            $table->dropColumn('no_driver_found_message');
            $table->dropColumn('no_pending_found_message');
            $table->dropColumn('driver_pending_date_label');
            $table->dropColumn('request_transfer_label');
            $table->dropColumn('no_balance_found_message');
            $table->dropColumn('no_paid_out_message');
            $table->dropColumn('no_reward_found_message');
            $table->dropColumn('no_my_ride_message');
            $table->dropColumn('no_more_data_message');

        });
    }
};
