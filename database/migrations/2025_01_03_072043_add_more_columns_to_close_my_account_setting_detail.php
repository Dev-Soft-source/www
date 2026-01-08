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
        Schema::table('close_my_account_setting_detail', function (Blueprint $table) {
            $table->text('web_closing_account_reason_label')->nullable();
            $table->text('web_irreversible_label')->nullable();
            $table->text('close_account_sure_message_text')->nullable();
            $table->text('close_it_button_label')->nullable();
            $table->text('take_me_back_button_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('close_my_account_setting_detail', function (Blueprint $table) {
            //
        });
    }
};
