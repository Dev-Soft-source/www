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
            $table->text('verify_amount_error')->nullable();
            $table->text('select_bank_label')->nullable();
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
            $table->dropColumn(['select_bank_label', 'verify_amount_error']);
        });
    }
};
