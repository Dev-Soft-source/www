<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reset_password_page_setting_detail', function (Blueprint $table) {
            $table->text('password_do_not_match_error')->nullable()->after('confirm_password_error');
        });
    }

    public function down()
    {
        Schema::table('reset_password_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('password_do_not_match_error');
        });
    }
};
