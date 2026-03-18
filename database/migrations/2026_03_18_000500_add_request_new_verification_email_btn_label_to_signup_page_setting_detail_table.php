<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->text('request_new_verification_email_btn_label')->nullable()->after('after_button_label');
        });
    }

    public function down()
    {
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('request_new_verification_email_btn_label');
        });
    }
};

