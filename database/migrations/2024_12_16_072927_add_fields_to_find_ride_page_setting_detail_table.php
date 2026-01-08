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
            $table->text('clear_button_label')->nullable();
            $table->text('apply_button_label')->nullable();
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

           $table->dropColumn('apply_button_label');
           $table->dropColumn('clear_button_label');

        });
    }
};
