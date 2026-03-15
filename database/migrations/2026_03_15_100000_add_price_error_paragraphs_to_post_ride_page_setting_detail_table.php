<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('carpool_regulation_limit_message')->nullable()->after('agree_term_error');
            $table->text('max_price_per_seat_message')->nullable()->after('carpool_regulation_limit_message');
            $table->text('non_commercial_carpool_requirement_message')->nullable()->after('max_price_per_seat_message');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['carpool_regulation_limit_message', 'max_price_per_seat_message', 'non_commercial_carpool_requirement_message']);
        });
    }
};
