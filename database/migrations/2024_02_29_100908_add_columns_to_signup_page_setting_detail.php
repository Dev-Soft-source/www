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
            $table->string('first_name_label')->after('required_label')->nullable();
            $table->string('last_name_label')->after('first_name_placeholder')->nullable();
            $table->string('email_label')->after('last_name_placeholder')->nullable();
            $table->string('password_label')->after('email_placeholder')->nullable();
            $table->string('confirm_password_label')->after('password_placeholder')->nullable();
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
            $table->dropColumn(['first_name_label','last_name_label','email_label','password_label','confirm_password_label']);
        });
    }
};
