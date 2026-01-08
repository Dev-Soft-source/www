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
        Schema::table('login_page_setting_detail', function (Blueprint $table) {
            $table->string('email_label')->after('or_label')->nullable();
            $table->string('password_label')->after('email_placeholder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['email_label','password_label']);
        });
    }
};
