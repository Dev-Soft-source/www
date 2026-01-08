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
        Schema::table('my_email_address_setting_detail', function (Blueprint $table) {
            $table->string('current_email_label')->nullable();
            $table->string('new_email_label')->nullable();
            $table->string('confirm_email_label')->nullable();
            $table->string('save_btn_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_email_address_setting_detail', function (Blueprint $table) {

          $table->dropColumn('save_btn_label');
          $table->dropColumn('confirm_email_label');
          $table->dropColumn('new_email_label');
          $table->dropColumn('current_email_label');

        });
    }
};
