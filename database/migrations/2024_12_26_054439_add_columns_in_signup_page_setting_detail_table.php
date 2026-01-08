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
            $table->string('first_name_error')->nullable();
            $table->string('last_name_error')->nullable();
            $table->string('email_error')->nullable();
            $table->string('password_error')->nullable();
            $table->string('confirm_password_error')->nullable();
            $table->string('agree_terms_error')->nullable();
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
            $table->dropColumn(['agree_terms_error', 'confirm_password_error', 'password_error', 'email_error', 'last_name_error',
                'first_name_error'
            ]);
        });
    }
};
