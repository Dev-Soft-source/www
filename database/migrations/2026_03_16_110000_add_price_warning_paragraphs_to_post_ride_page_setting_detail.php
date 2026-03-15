<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('price_above_reimbursement_warning')->nullable()->after('price_warning_keep_current_btn_label');
            $table->text('price_reduction_suggestion_message')->nullable()->after('price_above_reimbursement_warning');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['price_above_reimbursement_warning', 'price_reduction_suggestion_message']);
        });
    }
};
