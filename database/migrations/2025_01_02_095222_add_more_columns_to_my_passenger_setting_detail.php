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
        Schema::table('my_passenger_setting_detail', function (Blueprint $table) {
            $table->text('web_back_button_label')->nullable();
            $table->text('no_show_passenger_label')->nullable();
            $table->text('web_i_reviewed_label')->nullable();
            $table->text('web_reviewd_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_passenger_setting_detail', function (Blueprint $table) {
            //
        });
    }
};
