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
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {

            $table->text('firm_cancellation_confirm_poup_heading')->nullable();
            $table->text('firm_cancellation_confirm_poup_text')->nullable();
            $table->text('firm_cancellation_confirm_poup_yes_label')->nullable();
            $table->text('firm_cancellation_confirm_poup_no_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            
            $table->dropColumn(['firm_cancellation_confirm_poup_heading', 'firm_cancellation_confirm_poup_text', 'firm_cancellation_confirm_poup_yes_label', 'firm_cancellation_confirm_poup_no_label']);
        });
    }
};
