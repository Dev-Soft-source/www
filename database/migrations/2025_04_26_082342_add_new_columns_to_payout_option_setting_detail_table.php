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
            $table->text('institute_no_error')->nullable();
            $table->text('branch_address_error')->nullable();
            $table->text('branch_no_error')->nullable();
        
        
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
            $table->dropColumn('institute_no_error');
            $table->dropColumn('branch_address_error');
            $table->dropColumn('branch_no_error');

        });
    }
};
