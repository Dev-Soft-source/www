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
            $table->text('institution_number_label')->nullable();
            $table->text('branch_address_label')->nullable();
            $table->text('branch_number_label')->nullable();
            $table->text('branch_address_placeholder')->nullable();
            $table->text('account_address_placeholder')->nullable();
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
            $table->dropColumn(['institution_number_label', 'branch_address_label', 'branch_number_label', 'branch_address_placeholder', 'account_address_placeholder']);
        });
    }
};
