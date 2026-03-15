<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->string('delete_stop_modal_no_btn')->nullable()->after('price_error_adjust_btn_label');
            $table->string('delete_stop_modal_yes_btn')->nullable()->after('delete_stop_modal_no_btn');
            $table->string('price_warning_heading')->nullable()->after('delete_stop_modal_yes_btn');
            $table->string('price_warning_adjust_btn_label')->nullable()->after('price_warning_heading');
            $table->string('price_warning_keep_current_btn_label')->nullable()->after('price_warning_adjust_btn_label');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'delete_stop_modal_no_btn',
                'delete_stop_modal_yes_btn',
                'price_warning_heading',
                'price_warning_adjust_btn_label',
                'price_warning_keep_current_btn_label',
            ]);
        });
    }
};
