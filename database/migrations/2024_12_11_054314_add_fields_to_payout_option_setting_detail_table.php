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
            $table->string('bank_account_heading')->nullable();
            $table->string('update_btn_label')->nullable();
            $table->string('save_btn_label')->nullable();

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
            $table->dropColumn('bank_account_heading');
            $table->dropColumn('update_btn_label');
            $table->dropColumn('save_btn_label');
        });
    }
};
