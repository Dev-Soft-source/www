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
        Schema::table('my_phone_no_setting_detail', function (Blueprint $table) {
            $table->string('phone_no_description_text1')->nullable();
            $table->string('verified_number_label')->nullable();
            $table->string('default_verified_number_label')->nullable();
            $table->string('set_as_default_label')->nullable();
            $table->string('resend_code_btn_label')->nullable();
            $table->string('request_code_text')->nullable();
            $table->string('second_text')->nullable();
            $table->string('verify_phone_number_label')->nullable();
            $table->string('enter_code_label')->nullable();
            $table->string('otp_code_description')->nullable();
            $table->string('verify_phone_number_heading')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_phone_no_setting_detail', function (Blueprint $table) {

            $table->dropColumn('phone_no_description_text1');
            $table->dropColumn('verified_number_label');
            $table->dropColumn('default_verified_number_label');
            $table->dropColumn('set_as_default_label');
            $table->dropColumn('resend_code_btn_label');
            $table->dropColumn('request_code_text');
            $table->dropColumn('second_text');
            $table->dropColumn('verify_phone_number_label');
            $table->dropColumn('enter_code_label');
            $table->dropColumn('otp_code_description');
            $table->dropColumn('verify_phone_number_heading');

        });
    }
};
