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
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('from_field_icon')->nullable();
            $table->text('swap_field_icon')->nullable();
            $table->text('to_field_icon')->nullable();
            $table->text('date_field_icon')->nullable();
            $table->text('search_field_icon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['search_field_icon', 'date_field_icon', 'to_field_icon', 'swap_field_icon', 'from_field_icon']);
        });
    }
};
