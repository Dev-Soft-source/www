<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('price_error_heading')->nullable()->after('non_commercial_carpool_requirement_message');
            $table->text('price_error_adjust_btn_label')->nullable()->after('price_error_heading');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['price_error_heading', 'price_error_adjust_btn_label']);
        });
    }
};
