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
            $table->string('no_account_label')->after('signup_label')->nullable();
            $table->string('signup_link_label')->after('no_account_label')->nullable();
            $table->string('now_label')->after('signup_link_label')->nullable();
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
            $table->dropColumn(['now_label', 'signup_link_label', 'no_account_label']);
        });
    }
};
