<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->string('email_label')->nullable()->after('last_name_placeholder');
        });
    }

    public function down()
    {
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['email_label']);
        });
    }
};
