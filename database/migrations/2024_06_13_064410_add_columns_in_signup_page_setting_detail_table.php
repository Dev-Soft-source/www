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
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->string('phone_number_label')->after('confirm_password_placeholder')->nullable();
            $table->string('phone_number_option1')->after('phone_number_label')->nullable();
            $table->string('phone_number_option2')->after('phone_number_option1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['phone_number_option2','phone_number_option1','phone_number_label']);
        });
    }
};
